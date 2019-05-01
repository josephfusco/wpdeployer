<?php

namespace Deployer\Storage;

use Deployer\Git\Repository;
use Deployer\Git\RepositoryFactory;
use Deployer\Plugin;

class PluginRepository {

	/**
	 * @var RepositoryFactory
	 */
	private $repositoryFactory;

	/**
	 * @param RepositoryFactory $repositoryFactory
	 */
	public function __construct( RepositoryFactory $repositoryFactory ) {
		$this->repositoryFactory = $repositoryFactory;
	}

	public function allDeployerPlugins() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		global $wpdb;

		$table_name = deployerTableName();

		$rows = $wpdb->get_results( "SELECT * FROM $table_name WHERE type = 1" );

		$plugins = [];

		foreach ( $rows as $row ) {

			// This is our change to do some cleaning up
			if ( ! file_exists( WP_PLUGIN_DIR . '/' . $row->package ) ) {
				$this->delete( $row->id );
				continue;
			}

			$array                    = get_plugin_data( WP_PLUGIN_DIR . '/' . $row->package );
			$plugins[ $row->package ] = Plugin::fromWpArray( $row->package, $array );
			$repository               = new Repository( $row->repository );
			$repository->setBranch( $row->branch );
			$plugins[ $row->package ]->setRepository( $repository );
			$plugins[ $row->package ]->setPushToDeploy( $row->ptd );
			$plugins[ $row->package ]->setHost( $row->host );
			$plugins[ $row->package ]->setSubdirectory( $row->subdirectory );
		}

		return $plugins;
	}

	public function delete( $id ) {
		global $wpdb;

		$table_name = deployerTableName();

		$wpdb->delete( $table_name, [ 'id' => sanitize_text_field( $id ) ] );
	}

	public function unlink( $file ) {
		global $wpdb;

		$table_name = deployerTableName();

		$wpdb->delete( $table_name, [ 'package' => sanitize_text_field( $file ) ] );
	}

	public function editPlugin( $file, $input ) {
		global $wpdb;

		$model = new PackageModel(
			[
				'package'      => $file,
				'repository'   => $input['repository'],
				'branch'       => $input['branch'],
				'ptd'          => $input['ptd'],
				'subdirectory' => $input['subdirectory'],
			]
		);

		$table_name = deployerTableName();

		return $wpdb->update(
			$table_name,
			[
				'repository'   => $model->repository,
				'branch'       => $model->branch,
				'ptd'          => $model->ptd,
				'subdirectory' => $model->subdirectory,
			],
			[ 'package' => $model->package ]
		);
	}

	/**
	 * @param $slug
	 * @return Plugin
	 */
	public function fromSlug( $slug ) {
		$plugins = get_plugins();

		foreach ( $plugins as $file => $pluginInfo ) {
			$tmp         = explode( '/', $file );
			$currentSlug = $tmp[0];

			if ( $currentSlug === $slug ) {
				break;
			}

			$file = null;
		}

		return Plugin::fromWpArray( $file, $pluginInfo );
	}

	/**
	 * @param $repository
	 * @return Plugin $plugin
	 * @throws PluginNotFound
	 */
	public function deployerPluginFromRepository( $repository ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		global $wpdb;

		$table_name = deployerTableName();

		$model = new PackageModel( [ 'repository' => $repository ] );

		$row = $wpdb->get_row( "SELECT * FROM $table_name WHERE type = 1 AND repository = '{$model->repository}'" );

		if ( ! $row or ! file_exists( WP_PLUGIN_DIR . '/' . $row->package ) ) {
			throw new PluginNotFound( 'Could not find plugin.' );
		}

		$array  = get_plugin_data( WP_PLUGIN_DIR . '/' . $row->package );
		$plugin = Plugin::fromWpArray( $row->package, $array );

		$repository = $this->repositoryFactory->build(
			$row->host,
			$row->repository
		);

		$repository->setBranch( $row->branch );
		$plugin->setRepository( $repository );
		$plugin->setPushToDeploy( $row->ptd );
		$plugin->setHost( $row->host );
		$plugin->setSubdirectory( $row->subdirectory );

		if ( $row->private ) {
			$plugin->repository->makePrivate();
		}

		return $plugin;
	}

	public function store( Plugin $plugin ) {
		global $wpdb;

		$model = new PackageModel(
			[
				'package'      => $plugin->file,
				'repository'   => $plugin->repository,
				'branch'       => $plugin->repository->getBranch(),
				'status'       => 1,
				'host'         => $plugin->repository->code,
				'private'      => $plugin->repository->isPrivate(),
				'ptd'          => $plugin->pushToDeploy,
				'subdirectory' => $plugin->getSubdirectory(),
			]
		);

		$table_name = deployerTableName();

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE package = '$model->package'" );

		if ( $count !== '0' ) {

			return $wpdb->update(
				$table_name,
				[
					'branch'       => $model->branch,
					'status'       => $model->status,
					'subdirectory' => $model->subdirectory,
				],
				[ 'package' => $model->package ]
			);

		}

		return $wpdb->insert(
			$table_name,
			[
				'package'      => $model->package,
				'repository'   => $model->repository,
				'branch'       => $model->branch,
				'type'         => 1,
				'status'       => $model->status,
				'host'         => $model->host,
				'private'      => $model->private,
				'ptd'          => $model->ptd,
				'subdirectory' => $model->subdirectory,
			]
		);
	}
}
