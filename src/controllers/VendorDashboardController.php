<?php

namespace controllers;
use core\Controller;
use core\Request;
use core\Response;

class VendorDashboardController extends Controller
{

  public function index()
  {
    $this
      ->setData('title', 'Dashboard Venditore')
      ->setLayout('main')
      ->render('dashboard-venditore');
  }
}
