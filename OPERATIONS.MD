# Operations
useful information for operations.

## Installation

Recommended is using `famoser/agnes` which ensures folders are setup correctly and migrations are run.

Manual installation:
- unzip the release in the target directory
- create a .env.local in the target directory to overwrite configuration values of the .env file
- migrate the database (run `composer install` then `php bin/console doctrine:migrations:migrate -n`)
- point the server inside the `public` directory

The server must fulfil requirements of `composer.json`  

a sample prod `.env.local` could look like this:

```dotenv
APP_ENV=prod
APP_SECRET=NEW_SECRET
MAILER_URL=smtp://info@example.com:NOT_REAL@smtp.mail.com:465/?encryption=ssl&auth_mode=login
```

## Backup

you only need to backup the files in the `/var/persistent` folder.
it even includes the database :)

Using rsync, do something like this: 
```
rsync -azP ~/www/registration.egoi.org/production/shared/var/persistent/ user@IP:~/backup/
```

### Setup ssh

`ssh-copy-id -i ~/.ssh/id_rsa.pub username@domain` to add ssh key  
`cat ~/.ssh/id_rsa.pub` to query the active ssh key  
`ssh-keygen -t rsa -b 4096 -C "username@domain" && eval $(ssh-agent -s) && ssh-add ~/.ssh/id_rsa` generate a new key & add it to ssh  
