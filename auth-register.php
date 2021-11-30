<?php  
require_once "action/DbClass.php";

if($_SESSION['login_stiker_admin'] == true ){
  header('Location: index');
  exit();
}
$config = new ConfigClass();

$error_notif = "";
if(isset($_POST['auth-registion'])){
  $username = strtolower($_POST['username']);
  $useremail = $_POST['useremail'];
  $usertoko = $_POST['usertoko'];
  $userpassword = $_POST['userpassword'];

  // cek username
  $usernamecheck = $config->selectTable("user_galeri","username_user",$username);
  if(mysqli_num_rows($usernamecheck) > 0){
    $error_notif = "<strong>Mohon Maaf!</strong> Username yang anda masukkan sudah terdaftar";
  }else{
    $useremailcheck = $config->selectTable("user_galeri","email_user",$useremail);
    if(mysqli_num_rows($useremailcheck) > 0){
      $error_notif = "<strong>Mohon Maaf!</strong> Email yang anda masukkan sudah terdaftar";
    }else{
      $usertokocheck = $config->selectTable("user_galeri","toko_user",$usertoko);
      if(mysqli_num_rows($usertokocheck) > 0){
        $error_notif = "<strong>Mohon Maaf!</strong> Nama toko yang anda masukkan sudah terdaftar";
      }else{
        $pass_hash = password_hash($userpassword, PASSWORD_DEFAULT);
        $save = $config->insertUser("register",$username,$useremail,$usertoko,$pass_hash);
        if(!$save){
          $error_notif = "<strong>Mohon Maaf!</strong> pendaftaran gagal";
        }else{
          header('Location: auth-login');
          exit();
        }
      }
    }
  }
}

?> 
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | REGISTER</title>
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
                <h4 class="font-size-18 text-center mt-2">
                  REGISTER TO DECALSYSTEM
                </h4>
                <p class="text-muted text-center mb-4">
                  Aplikasi CRM untuk Percetakan dan StikerArt
                </p>
                <form class="form-horizontal" method="post" action="auth-register" autocomplete="off">
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
                        <label class="form-label" for="useremail">Email</label>
                        <input
                          type="email"
                          class="form-control"
                          required
                          id="useremail"
                          name="useremail"
                          value="<?= $_POST['useremail'] ?>"
                          placeholder="Enter email"
                        />
                      </div>
                      <div class="mb-4">
                        <label class="form-label" for="usertoko"
                          >Nama Toko</label
                        >
                        <input
                          type="text"
                          class="form-control"
                          required
                          id="usertoko"
                          name="usertoko"
                          value="<?= $_POST['usertoko'] ?>"
                          placeholder="Enter Nama Toko"
                        />
                      </div>
                      <div class="mb-4">
                        <label class="form-label" for="userpassword"
                          >Password</label
                        >
                        <input
                          type="password"
                          class="form-control"
                          required
                          id="userpassword"
                          name="userpassword"
                          placeholder="Enter password"
                        />
                      </div>
                      <div class="mb-4">
                        <label class="form-label" for="userpasswordconfirm"
                          >Confirm Password</label
                        >
                        <input
                          type="password"
                          class="form-control"
                          required
                          id="userpasswordconfirm"
                          placeholder="Enter password"
                        />
                      </div>
                      <div class="d-grid mt-4">
                        <button class="btn btn-primary waves-effect waves-light" name="auth-registion" type="submit">
                          Register
                        </button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <div class="mt-5 text-center">
              <p class="text-white-50">
                Already have an account ?<a
                  href="auth-login"
                  class="fw-medium text-primary"
                >
                  Login
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
    <script>
      var password = document.getElementById("userpassword")
      var confirm_password = document.getElementById("userpasswordconfirm");

      function validatePassword(){
        if(password.value != confirm_password.value) {
          confirm_password.setCustomValidity("Password Konfirmasi Berbeda");
        } else {
          confirm_password.setCustomValidity('');
        }
      }

      password.onchange = validatePassword;
      confirm_password.onkeyup = validatePassword;
    </script>

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