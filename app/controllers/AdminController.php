<?php

namespace Simple\App\Controllers;

use Simple\Core\App;
use Simple\App\Models\Feature;

class AdminController extends Controller
{

  /**
   * Access to the dashboard if already logged or if the credentials are correct
   *
   * @return mixed
   * @throws \Exception
   */
  public function dashboard()
  {

    // Check if the user is already logged in and the credentials are correct
    if ((isset($_SESSION['username']) && isset($_SESSION['password']))
      && ($_SESSION['username'] === App::get('config')['admin']['username'])
      && (($_SESSION['password'] === App::get('config')['admin']['password'])))
    {

      $page = 'Administration';
      $features = App::get('database')->selectAll('features', Feature::class);

      return $this->render('admin.dashboard', compact('page', 'features'));

    }

    // If not ask for credentials
    elseif (!isset($_POST['username'])
            OR $_POST['username'] != App::get('config')['admin']['username']
            OR !isset($_POST['password'])
            OR $_POST['password'] != App::get('config')['admin']['password'])
    {

      $page = 'Login';
      return view('admin.login', compact('page'));

    }

      // Log the user if the credentials are correct
     else {

      $_SESSION['username'] = $_POST['username'];
      $_SESSION['password'] = $_POST['password'];

      $token = bin2hex(openssl_random_pseudo_bytes(24));
      $_SESSION['token'] = $token;

      $page = 'Administration';
      $features = App::get('database')->selectAll('features', Feature::class);

      return $this->render('admin.dashboard', compact('page', 'features'));

    }

  }

  /**
   * Log out the current user
   */
  public function logout()
  {
    session_unset();
    session_destroy();

    return redirect('');
  }

  /**
   * Admin page for the features
   * @return mixed
   * @throws \Exception
   */
  public function features()
  {
    // If the user is already logged in and the credentials are correct
    if ((isset($_SESSION['username']) && isset($_SESSION['password']))
          && ($_SESSION['username'] === App::get('config')['admin']['username'])
          && (($_SESSION['password'] === App::get('config')['admin']['password'])))
    {
      // Allow the user to administrate features
      $page = 'Admin Features';
      $features = App::get('database')->selectAll('features', Feature::class);
      return view('admin.features', compact('page', 'features'));
    }
    else {
      // Ask for credentials
      $page = 'Login';
      return view('admin.login', compact('page'));
    }
  }

  /**
   * Show details of a given feature in the administration
   * GET admin-features/{id}
   * @param $id
   * @return mixed
   * @throws \Exception
   */
  public function showFeature($id)
  {
    // If the user is already logged in and the credentials are correct
    if ((isset($_SESSION['username']) && isset($_SESSION['password']))
          && ($_SESSION['username'] === App::get('config')['admin']['username'])
          && (($_SESSION['password'] === App::get('config')['admin']['password'])))
    {
      // Allow the user to see the details of a feature
      $feature = App::get('database')->select('features', $id, Feature::class);
      if ($feature) {
        $page = 'Admin • '.$feature->title;
        return view('admin.feature', compact('page', 'feature'));
      }
      return view('pages.error');
    }
    else {
      // Ask for credentials
      $page = 'Connexion';
      return view('admin.login', compact('page'));
    }
  }
}