<?php  
require_once "action/DbClass.php";

if($_SESSION['login_stiker_admin'] == true ){
  header('Location: index');
  exit();
}

$config = new ConfigClass();

$error_notif = "";
if(isset($_POST['auth-login'])){
  $username = strtolower($_POST['username']);
  $userpassword = $_POST['userpassword'];

  $check = $config->selectTable("user_galeri","username_user",$username);
  if(mysqli_num_rows($check) == 0){
    $error_notif = "<strong>Mohon Maaf!</strong> Username anda salah.";
  }else{
    $row = mysqli_fetch_assoc($check);
    $passdb = $row['pass_user'];
    if(!password_verify($userpassword, $passdb)){
      $error_notif = "<strong>Mohon Maaf!</strong> Password anda salah.";
    }else{
      $_SESSION['login_stiker_admin'] = true;
      $_SESSION['login_stiker_id'] = $row['id_user'];
      if($row['level_user'] == "Desainer" || $row['level_user'] == "Produksi" || $row['level_user'] == "Pemasang"){
        if($row['level_user'] == "Desainer"){
          header('Location: menunggu_designer');
          exit();
        }elseif($row['level_user'] == "Produksi"){
          header('Location: siap-cetak');
          exit();
        }else{
          header('Location: siap-dipasang');
          exit();
        }
      }else{
          header('Location: index');
          exit();
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | LOGIN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      content="APLIKASI CRM PERCETAKAN DAN STICKERART NO.1 INDONESIA"
      name="description"
    />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" />

    <!-- Bootstrap Css -->
    <link
      href="assets/css/bootstrap.min.css"
      id="bootstrap-style"
      rel="stylesheet"
      type="text/css"
    />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link
      href="assets/css/app.min.css"
      id="app-style"
      rel="stylesheet"
      type="text/css"
    />
  </head>

  <body class="bg-pattern">
    <div class="bg-overlay"></div>
    <div class="account-pages my-5 pt-5">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-xl-4 col-lg-6 col-md-8">
            <div class="card">
              <div class="card-body p-4">
                <div class="">
                  <div class="text-center">
                    <a href="index.html" class="">
                      <img
                        src="assets/images/logo-dark.png"
                        alt=""
                        height="24"
                        class="auth-logo logo-dark mx-auto"
                      />
                      <img
                        src="assets/images/logo-light.png"
                        alt=""
                        height="24"
                        class="auth-logo logo-light mx-auto"
                      />
                    </a>
                  </div>
                  <!-- end row -->
                  <h4 class="font-size-18 text-muted mt-2 text-center">
                    Welcome Back !
                  </h4>
                  <p class="mb-5 text-center">Sign in to DecalSystem</p>
                  <form class="form-horizontal" action="auth-login" method="post" autocomplete="off">
                    <div class="row">
                      <div class="col-md-12">
                      <?php if($error_notif != ""){ ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          <?= $error_notif ?>
                        </div>
                      <?php } ?>
                        <div class="mb-4">
                          <label class="form-label" for="username"
                            >Username</label
                          >
                          <input
                            type="text"
                            class="form-control"
                            required
                            id="username"
                            name="username"
                            style="text-transform: lowercase;"
                            value="<?= $_POST['username'] ?>"
                            placeholder="Enter username"
                          />
                        </div>
                        <div class="mb-4">
                          <label class="form-label" for="userpassword"
                            >Password</label
                          >
                          <input
                            type="password"
                            required
                            class="form-control"
                            id="userpassword"
                            name="userpassword"
                            placeholder="Enter password"
                          />
                        </div>

                        <div class="row">
                          <div class="col-12">
                            <div class=" mt-3 mt-md-0">
                              <a href="auth-recoverpw" class="text-muted"
                                ><i class="mdi mdi-lock"></i> Forgot your
                                password?</a
                              >
                            </div>
                          </div>
                        </div>
                        <div class="d-grid mt-4">
                          <button
                            class="btn btn-primary waves-effect waves-light"
                            type="submit"
                            name="auth-login"
                          >
                            Log In
                          </button>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="mt-5 text-center">
              <p class="text-white-50">
                Don't have an account ?
                <a href="auth-register" class="fw-medium text-primary">
                  Register
                </a>
              </p>
              <p class="text-white-50">
                ©
                <script>
                  document.write(new Date().getFullYear());
                </script>
                BEEDECAL. Crafted with
                <i class="mdi mdi-heart text-danger"></i> by GALERIIDE
              </p>
            </div>
          </div>
        </div>
        <!-- end row -->
      </div>
    </div>
    <!-- end Account pages -->

    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>

    <script src="assets/js/app.js"></script>
    <script>
      document.querySelector('#username').addEventListener('keydown', function(e) {

      if (e.which === 32) {
          e.preventDefault();
      }
    });
    </script>
  </body>
</html>
<?php mysqli_close($db->conn) ?>