DB_CONFIG="tests/config/dbconfig.xml"
TEST_DIR="tests/"
AUTOLOADER="vendor/autoload.php"
PHP_UNIT="vendor/bin/phpunit"

if [ ! -f ${PHP_UNIT} ]
then
    echo "Cannot find phpunit, start composer install/update"
    exit 1
fi

echo ""
echo "-----------"
echo "PHPUnit : ${PHP_UNIT}"
echo "Database configuration file : ${DB_CONFIG}"
echo "Autoloader : ${AUTOLOADER}"
echo "Directory to test : ${TEST_DIR}"
echo "-----------"
echo ""

${PHP_UNIT} --bootstrap ${AUTOLOADER} --configuration ${DB_CONFIG} ${TEST_DIR}