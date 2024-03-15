<?php

namespace Your\Namespace;

use \Controller;
use \Language;
use \Document;
use \URL;
use \Session;
use \Request;
use \User;

class DashboardOnlineController extends Controller {

    /**
     * @var Language
     */
    private $language;

    /**
     * @var Document
     */
    private $document;

    /**
     * @var URL
     */
    private $url;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var User
     */
    private $user;

    /**
     * @var array
     */
    private $error = [];

    /**
     * DashboardOnlineController constructor.
     *
     * @param Language $language
     * @param Document $document
     * @param URL $url
     * @param Session $session
     * @param Request $request
