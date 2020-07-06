#!/bin/bash

# --- Function definition --- #

# Display the usage of the script
diplayUsage() {
    echo 'Usage: ./install.sh'
    echo '    MANDATORY PARAMETERS'
    echo '        none'
    echo '    OPTIONNAL PARAMETERS'
    echo '        -n'
    echo '        --no-interaction'
    echo '                Run the installation in no interaction mode.'
    echo '        --database-host'
    echo '                Host of the database. Can be an ipv4 address or an URL. Mandatory if --no-interaction is selected.'
    echo '        --database-port'
    echo '                Port of the database. Only numbers are allowed. Mandatory if --no-interaction is selected.'
    echo '        --database-name'
    echo '                Name of the database. Mandatory if --no-interaction is selected.'
    echo '        --database-user'
    echo '                User to connect to the database. Mandatory if --no-interaction is selected.'
    echo '        --database-password'
    echo '                Password to connect to the database. Mandatory if --no-interaction is selected.'
    echo '        -u'
    echo '        --admin-username'
    echo '                Username of the Structura administrator. Mandatory if --no-interaction is selected.'
    echo '        -p'
    echo '        --admin-password'
    echo '                Password of the Structura administrator. Mandatory if --no-interaction is selected.'
    echo '        -h'
    echo '        --help'
    echo '                Display this manual.'
}

# --- Check if admin usename or password start with a - and throw an error if so --- #
check_admin_login_data() {
    echo 'Checking if admin login is starting with a hyphen'

    if [[ $admin_username == -* ]];
    then
        echo '[ERROR] This tool does not support currently admin username starting with an hyphen.'
        echo 'Exiting the script'
        exit 2
    fi
    if [[ $admin_password == -* ]];
    then
        echo '[ERROR] This tool does not support currently admin password starting with an hyphen.'
        echo 'Exiting the script'
        exit 2
    fi
}

# Ask user input of missing arguments
ask_user_input() {
    echo
    echo 'Please enter the following information:'

    if [ -z "$admin_username" ]
    then
        read -p 'Structura administrator username : ' admin_username
    fi

    if [ -z "$admin_password" ]
    then
        read -sp 'Structura administrator password : ' admin_password
        # Since the input is hidden for security reasons, we need to write to the new line
        echo
    fi

    if [ "$database_host" = "" ]
    then
        # Ask and read database_host with default value 127.0.0.1
        read -p 'Database host [127.0.0.1] : ' database_host
        database_host=${database_host:-'127.0.0.1'}
    fi

    if [ "$database_port" = "" ]
    then
        # Ask and read database_port with default value 3306
        read -p 'Database port [3306] : ' database_port
        database_port=${database_port:-3306}
    fi

    if [ "$database_user" = "" ]
    then
        # Ask and read database_user, no default value
        read -p 'Database user : ' database_user
    fi

    if [ "$database_password" = "" ]
    then
        # Ask and read database_password with no default value
        read -sp 'Database password : ' database_password
        # Since the input is hidden for security reasons, we need to write to the new line
        echo
    fi

    if [ "$database_name" = "" ]
    then
        # Ask and read database_name with default value erp-asso-test
        read -p 'Database name [structura] : ' database_name
        database_name=${database_name:-structura}
    fi
}

# Create a .env.local file
setup_env_file() {
    env_file_name=".env.local"

    # Creation the file
    touch $env_file_name

    # Content is writen in the file
    echo "###> doctrine/doctrine-bundle ###" >> $env_file_name
    echo "DATABASE_URL=$database_url" >> $env_file_name
    echo "###< doctrine/doctrine-bundle ###" >> $env_file_name
    echo "" >> $env_file_name
    echo "###> symfony/swiftmailer-bundle ###" >> $env_file_name
    echo "MAILER_URL=null://localhost" >> $env_file_name
    echo "###< symfony/swiftmailer-bundle ###" >> $env_file_name
}

# --- Parsing arguments --- #
for i in "$@"
do
    case $i in
        -h*)
        help=1
        ;;
        --help*)
        help=1
        ;;
        -n*)
        no_interaction=1
        ;;
        --no-interaction*)
        no_interaction=1
        ;;
        -u=*)
        admin_username="${i#*=}"
        shift # past argument=value
        ;;
        --admin-username=*)
        admin_username="${i#*=}"
        shift # past argument=value
        ;;
        -p=*)
        admin_password="${i#*=}"
        shift # past argument=value
        ;;
        --admin-password=*)
        admin_password="${i#*=}"
        shift # past argument=value
        ;;
        *)
        # unknown argument or option
        ;;
    esac
done

# --- Checking if the user needs help --- #
if [ "$help" == 1 ]
then
    diplayUsage
    exit
fi
# --- No interaction mode --- #
if [ "$no_interaction" == 1 ]
then
    if [ -z "$admin_username" ]
    then
        echo 'Missing administrator username argument.'
        diplayUsage
        echo 'Exiting the script'
        exit 2
    fi

    if [ -z "$admin_password" ]
    then
        echo 'Missing administrator password argument.'
        diplayUsage
        echo 'Exiting the script'
        exit 2
    fi

    if [ "$database_host" = "" ]
    then
        echo "Argument database-host is mandatory."
        diplayUsage
        echo 'Exiting the script'
        exit 2
    fi

    if [ "$database_port" = "" ]
    then
        echo "Argument database-port is mandatory."
        diplayUsage
        echo 'Exiting the script'
        exit 2
    fi

    if [ "$database_user" = "" ]
    then
        echo "Argument database-user is mandatory."
        diplayUsage
        echo 'Exiting the script'
        exit 2
    fi

    if [ "$database_password" = "" ]
    then
        echo "Argument database-password is mandatory."
        diplayUsage
        echo 'Exiting the script'
        exit 2
    fi

    if [ "$database_name" = "" ]
    then
        echo "Argument database-name is mandatory."
        diplayUsage
        echo 'Exiting the script'
        exit 2
    fi
fi

# --- Setup .env file --- #
# --- No interaction mode --- #
if [ "$no_interaction" == 1 ]
then
    # Concatening previous data to form the database connection string (also named url) for doctrine
    database_url="mysql://${database_user}:${database_password}@${database_host}:${database_port}/${database_name}"

    # Generating env file
    setup_env_file

# --- Interactive mode --- #
else
    # Asking user environment informations
    ask_user_input

    # Concatening previous data to form the database connection string (also named url) for doctrine
    database_url="mysql://${database_user}:${database_password}@${database_host}:${database_port}/${database_name}"

    # Generating env and php unit files
    setup_env_file
fi

# --- Check admin data --- #
check_admin_login_data || exit

# --- Install using Composer --- #
echo 'Installing project dependencies...'
composer install || exit

# --- Create database --- #
bin/console doctrine:database:create || exit

# --- Create database schema --- #
bin/console doctrine:migration:migrate -n || exit

# --- Create admin account --- #
bin/console app:create-admin-account "${admin_username}" "${admin_password}" || exit

# --- Display warning message about Messages worker --- #
# --- Define font settings used to display messages --- #
defaultFontSettings='\033[0m'
orangeBackground='\e[48;5;202m'
bold='\033[1m'

echo ''
echo -e "${orangeBackground}${bold}[ WARNING ]${defaultFontSettings} This software needs the Symfony messenger deamon to run properly."
echo -e "${orangeBackground}${bold}[ WARNING ]${defaultFontSettings} You need to run it using: ${bold}bin/console messenger:consume${defaultFontSettings}"
echo -e "${orangeBackground}${bold}[ WARNING ]${defaultFontSettings} Make sure to relaunch it if you reboot your server."

exit
