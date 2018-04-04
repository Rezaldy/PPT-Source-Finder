# PPT-Source-Finder
Upload Powerpoint files and save their sources one by one

Requirements:

- Unix distribution able to run Libreoffice, Java and Python.
- PHP 5.3+, preferrably PHP7.1+
- Zip extension
- XML Parser extension
- XMLWriter extension
- GD

Install the following for the use of the [Lowrapper](https://github.com/mnvx/lowrapper) library:

```sh
sudo add-apt-repository ppa:libreoffice/ppa
sudo apt-get update
sudo apt-get install default-jdk -y
sudo apt-get install python-software-properties  -y
sudo apt-get install software-properties-common -y
sudo apt-get install libreoffice-core --no-install-recommends
sudo apt-get install libreoffice-writer
composer require mnvx/lowrapper
```