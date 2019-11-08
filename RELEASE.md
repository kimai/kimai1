RELEASE
=======

Versions up to 1.3.2 provide a file named "kimai_{version}.zip".
For newer versions, you'll have to create this file manually.
I've done it using this description on an ubuntu 18.04 system.

Install Various Dependencies
----------------------------

```
sudo apt-get install -y ant
sudo apt-get install -y composer
sudo apt-get install -y php7.2
sudo apt-get install -y php7.2-mysql
sudo apt-get install -y php7.2-xml
sudo apt-get install -y php7.2-ldap
sudo apt-get install -y php7.2-mbstring
```

Checkout Release Tag
--------------------

```
cd kimai
git checkout -b v1.3.5 v1.3.5
```

Run Composer
------------

```
composer  install --no-dev
```

Build The Zip file
------------------

```
ant build
```

This zip file will be created: /tmp/kimai/kimai_1.3.5.zip
