<?php

namespace Deployer\Storage;

use Deployer\Git\Repository;
use Deployer\Git\RepositoryFactory;
use Deployer\Theme;

class ThemeRepository {

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

	public function allDeployerThemes() {
		global $wpdb;

		$table_name = deployerTableName();

		$rows = $wpdb->get_results( "SELECT * FROM $table_name WHERE type = 2" );

		$themes = [];

		foreach ( $rows as $row ) {

			// This is our time to do some cleaning up
			if ( ! file_exists( get_theme_root() . '/' . $row->package ) ) {
				$this->delete( $row->id );
				continue;
			}

			$object                  = wp_get_theme( $row->package );
			$themes[ $row->package ] = Theme::fromWpThemeObject( $object );
			$repository              = new Repository( $row->repository );
			$repository->setBranch( $row->branch );
			$themes[ $row->package ]->setRepository( $repository );
			$themes[ $row->package ]->setPushToDeploy( $row->ptd );
			$themes[ $row->package ]->setHost( $row->host );
			$themes[ $row->package ]->setSubdirectory( $row->subdirectory );
		}

		return $themes;
	}

	public function delete( $id ) {
		global $wpdb;

		$table_name = deployerTableName();

		$wpdb->delete( $table_name, [ 'id' => sanitize_text_field( $id ) ] );
	}

	public function unlink( $stylesheet ) {
		global $wpdb;

		$table_name = deployerTableName();

		$wpdb->delete( $table_name, [ 'package' => sanitize_text_field( $stylesheet ) ] );
	}

	public function editTheme( $stylesheet, $input ) {
		global $wpdb;

		$model = new PackageModel(
			[
				'package'      => $stylesheet,
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
	 * @return Theme
	 */
	public function fromSlug( $slug ) {
		$wpTheme = wp_get_theme( $slug );

		return Theme::fromWpThemeObject( $wpTheme );
	}

	/**
	 * @param $repository
	 * @return Theme
	 * @throws ThemeNotFound
	 */
	public function deployerThemeFromRepository( $repository ) {
		global $wpdb;

		$table_name = deployerTableName();

		$model = new PackageModel( [ 'repository' => $repository ] );

		$row = $wpdb->get_row( "SELECT * FROM $table_name WHERE type = 2 AND repository = '{$model->repository}'" );

		if ( ! $row or ! file_exists( get_theme_root() . '/' . $row->package ) ) {
			throw new ThemeNotFound( 'Couldn\'t find theme.' );
		}

		$object = wp_get_theme( $row->package );
		$theme  = Theme::fromWpThemeObject( $object );

		$repository = $this->repositoryFactory->build(
			$row->host,
			$row->repository
		);

		$repository->setBranch( $row->branch );
		$theme->setRepository( $repository );
		$theme->setPushToDeploy( $row->ptd );
		$theme->setHost( $row->host );
		$theme->setSubdirectory( $row->subdirectory );

		if ( $row->private ) {
			$theme->repository->makePrivate();
		}

		return $theme;
	}

	public function store( Theme $theme ) {
		global $wpdb;

		$model = new PackageModel(
			[
				'package'      => $theme->stylesheet,
				'repository'   => $theme->repository,
				'branch'       => $theme->repository->getBranch(),
				'status'       => 1,
				'host'         => $theme->repository->code,
				'private'      => $theme->repository->isPrivate(),
				'ptd'          => $theme->pushToDeploy,
				'subdirectory' => $theme->getSubdirectory(),
			]
		);

		$table_name = deployerTableName();

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE package = '$model->package'" );

		if ( $count !== '0' ) {

			return $wpdb->update(
				$table_name,
				[
					'status'       => $model->status,
					'branch'       => $model->branch,
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
				'type'         => 2,
				'status'       => $model->status,
				'host'         => $model->host,
				'private'      => $model->private,
				'ptd'          => $model->ptd,
				'subdirectory' => $model->subdirectory,
			]
		);
	}
}
