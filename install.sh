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
    echo '                Run the installation in no interaction mode. If selected, --admin-username and --admin-password are mandatory.'
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
    fi
    if [ -z "$admin_password" ]
    then
        echo 'Missing administrator password argument.'
    fi
    diplayUsage
    echo 'Exiting the script'
    exit 2
fi

# --- Install using Composer --- #
echo 'Installing project dependencies...'
composer install

exit
