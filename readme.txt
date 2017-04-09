=== WP Deployer ===
Tags: git, deploy, deployment, github, workflow
Requires at least: 3.9
Tested up to: 4.5
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Deploy directly from GitHub, Bitbucket, & GitLab. Never again copy files over FTP. It works everywhere - even on cheap shared hosting!

== Description ==

= Features =

* Install and update your WordPress themes and plugins directly from GitHub
* BitBucket and GitLab support
* Easy version control of your clients code
* Works everywhere because it hooks in to the WordPress core auto updater
* No Git or SSH needed on the server
* **Push-to-deploy** can automatically trigger updates when whenever you push to GitHub
* Support for branches

= Get started =

If you already use Git for your projects and your themes and plugins are in their own repositories on GitHub, getting started with WP Deployer is simple and easy. Just go to "New plugin" or "New theme" in the WP Deployer menu and type in the repository for the package: github-username/repository-name

If any of your plugins or themes are in private repositories on GitHub, WP Deployer will need a token to access them. You can read GitHub's guide to application tokens [here](https://help.github.com/articles/creating-an-access-token-for-command-line-use/). Paste in the token at WP Deployer settings page.

= Conventions =

* Theme stylesheets _must_ be named the same as the repository
* Plugin directories _must_ be named the same as the repository
* GitHub version tags _must_ be numeric, such as '1.0' or '1.0.1', with an optional preceding 'v', such as 'v1.0.1'
* WordPress version tags _must_ be numeric, such as '1.0' or '1.0.1'

= Git workflow =

The way WP Deployer works, packages (themes and plugins) need to be in their own repositories. If your packages are in their own repositories already, you can safely skip this section. Some developers prefer having their whole WordPress installation under Git, which potentially makes things a bit more complicated. By having all packages in their own repositories, you can easily share code across clients / projects. Since you shouldn’t be editing the core WordPress code, in most cases having the whole project under Git shouldn’t be necessary. However, if for some reason your project require that you have one Git repository for the whole project, you will have to use Git submodules, so that you can still have every package in its own (sub) repository.

== Installation ==

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `wp-deployer.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `wp-deployer.zip`
2. Extract the `wp-deployer` directory to your computer
3. Upload the `wp-deployer` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

== Screenshots ==

1. Plugins installed and managed with WP Deployer
2. The WP Deployer dashboard
3. Manage themes and plugins from the dashboard

== Changelog ==

= 1.0.0 =

* Initial Release
