<?php session_start();
if (isset($_SESSION['userid'])) {
    header('Location: ../index.php');
    exit();
}
if (isset($_SESSION['fb_user_id'])) {
    header('Location: ../index.php');
    exit();
}
// isset($_SESSION['userid']) ? header('Location: ../index.php') && exit() : header('');
require '../vendor/autoload.php';
$fb = new Facebook\Facebook([
    'app_id' => '6621295174664204',
    'app_secret' => 'f5801e183902397cb28ce980fae25af8',
    'default_graph_version' => 'v18.0',
]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['public_profile', 'email']; // Optional permissions
$loginUrl = $helper->getLoginUrl('http://localhost/movie-review/facebook-callback.php', $permissions);

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="../output.css" rel="stylesheet">
    <script src="../js/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/slick.css">
    <script src="../js/slick.min.js"></script>
    <script src="../js/002afb9e14.js"></script>
    <title>Login</title>
</head>

<body>
    <div class="flex flex-row justify-center items-center h-screen">
        <div class="grid grid-cols-1 p-10 md:p-16 rounded-lg bg-neutral md:w-7/12 xl:w-4/12">
            <form id="loginForm" class="form-control gap-2">
                <h1 class="text-center font-medium text-2xl uppercase">
                    Login
                </h1>
                <label class="label">
                    <span class="label-text">
                        Email address:
                    </span>
                </label>
                <input type="text" class="input input-bordered input-primary" name="email" id="email" required>
                <label class="label">
                    <span class="label-text">
                        Password:
                    </span>
                </label>
                <input type="password" class="input input-bordered input-primary" name="password" id="password"
                    required>
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="../signup">
                    Don't have an account?
                </a>
                <div class="flex flex-row justify-between">
                    <a href="<?php echo htmlspecialchars($loginUrl) ?>" class="btn btn-outline btn-sm btn-info"><i
                            class="fa-brands fa-facebook"></i>Sign in with
                        Facebook</a>
                    <a href="" class="btn btn-outline btn-sm btn-error"><i class="fa-brands fa-google"></i>Sign in with
                        Google</a>
                </div>
                <button type="submit" class="btn btn-primary my-4">
                    Login
                </button>
            </form>

        </div>
    </div>
</body>
<script>
    const [loginForm] = $('#loginForm');
    $(loginForm).on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '../api.php',
            type: 'POST',
            data: {
                login: true,
                email: $('#email').val(),
                password: $('#password').val()
            },
            success: function (response) {
                data = JSON.parse(response);
                console.log(data)
                if (data.error) {
                    alert(data.error);
                } else {
                    sessionStorage.setItem('isLoggedIn', 'true');
                    window.location.href = '../';
                }
            }
        })
    })
</script>

</html>