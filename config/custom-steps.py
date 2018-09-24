#!/usr/bin/env python 
#php path item
#install modman (brew install modman)
#install generate modman (https://github.com/mhauri/generate-modman)
#  curl -sS https://raw.githubusercontent.com/mhauri/generate-modman/master/generate-modman > generate-modman
#  sudo mv generate-modman /usr/local/bin
#  sudo chmod 755 /usr/local/bin/generate-modman
#

#unique microgroup items
#better way to reference /theme/app/design/frontend/microgroup/default/template/page/html/header.phtml

import ConfigParser
import StringIO
import getopt
import os
import sys
import time
def run_custom_steps(prop):
    os.system("""
            ENVIRO=%s
            source %s/include-common/load-ini/load_ini.sh

            IP=$(sh ${SS_PATH}/include-common/shellscripts/getmyip.sh)
            PORT=80

            HTTP_CONF=/etc/nginx/conf.morroni/${HOSTNAME}.conf

            HOSTNAME_DIR="/sites/${HOSTNAME}"
            SITE_DIR="${WEBROOT}${HOSTNAME_DIR}"
            cp -R modules-custom/aw_advancedreports-2.7.1_step1/* ./src/httpdocs/
            cp -R modules-custom/aw_advancedreports-2.7.1_step2/* ./src/httpdocs/
            cp -R modules-custom/Moogento_Email_step1/* ./src/httpdocs/
            cp -R modules-custom/Moogento_Email_step2/* ./src/httpdocs/
            cp -R modules-custom/Moogento_pickPack_step1/* ./src/httpdocs/
            cp -R modules-custom/Moogento_pickPack_step2/* ./src/httpdocs/
            cp -R modules-custom/Moogento_ShipEasy_step1/* ./src/httpdocs/
            cp -R modules-custom/Moogento_ShipEasy_step2/* ./src/httpdocs/            
#           cp -R modules-custom/authnet-pci/* ./src/httpdocs/
#           cp -R modules-custom/Fooman_GoogleAnalyticsPlus/* ./src/httpdocs/
#            cp -R modules-custom/magento-order-comment/* ./src/httpdocs/
#            cp -R modules-custom/Morroni_Customreports/* ./src/httpdocs/
#            cp -R modules-custom/Perfect_Watermarks/* ./src/httpdocs/
#            cp -R modules-custom/StackoverflowCheckout/* ./src/httpdocs/
#            cp -R modules-custom/tax_exempt_ce_v2.6.0/* ./src/httpdocs/
#            cp -R modules-custom/ups_shipment_labels/* ./src/httpdocs/
#            cp -R modules-custom/ups-sl-manager-pro/* ./src/httpdocs/


            echo "Core hack override"

            cp -R theme/app/code/local/Mage/* src/httpdocs/app/code/core/Mage/
            cp -R theme/app/code/local/Mage/* src/httpdocs/app/code/core/Zend/
            rm -rf src/httpdocs/app/code/local/Mage
            rm -rf src/httpdocs/app/code/local/Zend
            echo "creating grid cache folder"
            mkdir src/httpdocs/var/grid-cache
            mkdir -p src/httpdocs/media/upslabel/label

    """ % (ENVIRO, SS_PATH))
    return
