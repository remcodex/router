<?php

use Remcodex\Router\Command;
use Remcodex\Router\Listeners\HttpListener;
use Remcodex\Router\Listeners\ServerListener;


Command::listen('http.server.list', [ServerListener::class, 'list']);
Command::listen('http.request', [HttpListener::class, 'request']);