#!/bin/bash


# Export the database
echo 'Exporting database...'
mysqldump --add-drop-table -u sabr8669_mariepoule -p'$6tgz95CA$' -h localhost sabr8669_coworking > ~/database_dump.sql

# Replace all occurrences of 'prfxcrwkng_' with 'wp_' in the SQL file
echo 'Updating table prefix...'
sed -i 's/prfxcrwkng_/wp_/g' ~/database_dump.sql

# Import into the new database
echo 'Importing to wordpress.coworking-metz.fr...'
mysql -u coworking -pmrXVB2R9d39usDoPFGNe -h wordpress.coworking-metz.fr coworking < ~/database_dump.sql

echo 'Updating site URL...'
wget -qO- 'https://wordpress.coworking-metz.fr/wp-admin/?config=force&auto'

echo 'Import completed.'

echo 'Data rsync'
rsync -avh ~/htdocs/coworking-metz.fr/wp-content/uploads/ coworking@wordpress.coworking-metz.fr:/home/coworking/data/uploads/