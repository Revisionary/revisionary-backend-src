<?php

function load_session() {


    if (session_status() == PHP_SESSION_NONE) {

        session_name(session_name);
        session_save_path(sessiondir);
		ini_set('session.cookie_domain', '.'.domain);
		ini_set('session.gc_maxlifetime', session_lifetime);
        session_set_cookie_params(0, '/', '.'.domain);
        session_start();

    } else {
        if (session_name() != session_name) {

            session_destroy();
            session_name(session_name);
            session_save_path(sessiondir);
			ini_set('session.cookie_domain', '.'.domain);
			ini_set('session.gc_maxlifetime', session_lifetime);
            session_set_cookie_params(0, '/', '.'.domain);
            session_start();

        }
    }


}