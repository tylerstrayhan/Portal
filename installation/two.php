<?php
if(fSession::get('maxStep') < 2)
    fURL::redirect('?step=one');

$tpl = Util::newTpl($this, 'two');

/*
 * Validates the database data
 */
if(fRequest::isPost() && fRequest::get('db_submit')) {
    /*
     * Store input values
     */
    $tpl->set('host', fRequest::encode('host'));
    $tpl->set('user', fRequest::encode('user'));
    $tpl->set('pw', fRequest::encode('pw'));
    $tpl->set('database', fRequest::encode('database'));
    $tpl->set('prefix', fRequest::encode('prefix'));

    try {
        $vali = new fValidation();

        $vali->addRequiredFields(array(
                                      'host',
                                      'user',
                                      'pw',
                                      'database'
                                 ))
            ->addCallbackRule('host', Util::checkHost, 'Please enter an valid host.');

        $vali->setMessageOrder('host', 'user', 'pw', 'database', 'prefix')
            ->validate();

        $db = new fDatabase('mysql', fRequest::encode('database'),
                            fRequest::encode('user'),
                            fRequest::encode('pw'),
                            fRequest::encode('host'));
        $db->connect();
        $version = $db->translatedQuery(
            'SELECT `value` FROM "' . $tpl->get('prefix') . '_settings" WHERE `key` = %s', 'version')->fetchScalar();
        if($version <= 0)
            throw new fSQLException();
        $db->close();
    } catch(fValidationException $e) {
        fMessaging::create('validation', 'install/two', $e->getMessage());
    } catch(fConnectivityException $e) {
        fMessaging::create('connectivity', 'install/two', $e->getMessage());
    } catch(fAuthorizationException $e) {
        fMessaging::create('auth', 'install/two', $e->getMessage());
    } catch(fNotFoundException $e) {
        fMessaging::create('notfound', 'install/two', $e->getMessage());
    } catch(fEnvironmentException $e) {
        fMessaging::create('env', 'install/two', $e->getMessage());
    } catch(fProgrammerException $e) {
        fMessaging::create('prog', 'install/two', $e->getMessage());
    } catch(fSQLException $e) {
        fMessaging::create('nodb', 'install/two', fText::compose('No Database found'));
    }

    try {
        // checking db.php
        $db_file = new fFile(__INC__ . 'config/db.php');

        if(!fMessaging::check('validation', 'install/two')) {
            if($tpl->get('type') != 'sqlite')
                $host = $tpl->get('host');
            else $host = $tpl->get('dbfile');

            $contents =
                "<?php \n/*\n* Do not modify this unless you know what you are doing!\n*/\n\ndefine('DB_HOST', '" .
                $host . "');\ndefine('DB_USER', '" . $tpl->get('user') . "');\ndefine('DB_PW', '" . $tpl->get('pw') .
                "');\ndefine('DB_DATABASE', '" . $tpl->get('database') . "');\ndefine('DB_PREFIX', '" .
                $tpl->get('prefix') . "');\n";
            $db_file->write($contents);
        }
    } catch(fValidationException $e) {
        fMessaging::create('db_file', 'install/two', $e->getMessage());
    }
    if(!fMessaging::check('*', 'install/two')) {
        fSession::set('maxStep', 3);
        fURL::redirect('?step=three');
    }
}