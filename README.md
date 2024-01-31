# Portail web du coworking (wordpress / woocommerce)

This project holds the Coworking Metz website source code,
available at https://www.coworking-metz.fr.

## Getting Started

These instructions will give you a copy of the project up and running on
your local machine for development and testing purposes.

### Prerequisites

Requirements for the software and other tools to build, test and push

- [Git](https://git-scm.com/) - Version control system
- [Docker](https://www.docker.com/) - Container platform

### Data

Dump sql local: https://drive.google.com/open?id=126e3CxBWBrAbTK-H8Bh9VUSpKI0rFCQ_&authuser=coworking.metz%40gmail.com&usp=drive_fs

### Install
A step by step series of examples that tell you how to get a development environment running:
```bash
git clone git@gitlab.com:coworking-metz-poulailler/portail-coworking-metz.git
cd portail-coworking-metz
```

- Edit `/etc/hosts` to redirect `www.coworking-metz.local` to `127.0.0.1`
- Run `docker-compose up`
- Navigate to http://localhost:8180/index.php?route=/database/import&db=wordpress
- Select the [SQL file](#data)
- Submit

- Navigate to http://localhost:8180/index.php?route=/&route=%2F&db=wordpress&table=prfxcrwkng_users
- Edit the admin user (the one with id 1)
- Change its `user_pass` to `newpassword`
- Select `MD5` in the `Function` column
- Submit by clicking on the `Go` button at the bottom

- Navigate to http://www.coworking-metz.local/wp-admin/
- Enter the admin user email as username
- Enter `newpassword` as password
- Submit

Houra 🎉 You have successfully setup the project 🙌

### Start the project

Make sure Docker is running before starting.

```bash
docker-compose up -d
```

Then the website will be available at http://www.coworking-metz.local/.

### OAuth2

**DISCLAIMER**
This server use a plugin called `oauth2-provider` to serve as OAuth2 provider.
As the plugin is somewhat non free and requires a license to be fully functionnal,
you have 2 options to bypass restrictions on `grant_types`:
- comment the following code in `TokenController.php#190` to let any client ask for a new refresh token.
```php
/**
 * Validate the client can use the requested grant type
 */
// if ( ! $this->clientStorage->checkRestrictedGrantType( $clientId, $grantTypeIdentifier ) ) {
// 	$response->setError( 400, 'unauthorized_client', 'The grant type is unauthorized for this client_id' );
// 	return false;
// }
```
- or edit the client details directly in the database
```sql
UPDATE `prfxcrwkng_postmeta`
SET `meta_value` = 'a:3:{i:0;s:18:\"authorization_code\";i:1;s:8:\"implicit\";i:2;s:13:\"refresh_token\";}'
WHERE `prfxcrwkng_postmeta`.`meta_id` = 386442;
```

Update: This manual modification is no longer necessary, it is done by the code in mu-plugins/oauth2-provider.php

### Troubleshoot

If you are experiencing slowliness from loading files in Docker on macOS, you can set the `VirtioFS`
in the `General` settings of Docker.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

- [README-Template](https://github.com/PurpleBooth/a-good-readme-template) for what you're reading
