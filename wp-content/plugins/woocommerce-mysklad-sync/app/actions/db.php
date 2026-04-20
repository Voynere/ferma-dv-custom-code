<?php

use WCSTORES\WC\MS\Wordpress\Actions\Actions;

Actions::add('%is_table_checking_not', [new WCSTORES\WC\MS\Init\Tables\TableUpdate(), 'boot'],10,3);
