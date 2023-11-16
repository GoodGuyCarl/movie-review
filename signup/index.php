<?php session_start();
isset($_SESSION['userid']) ? header('Location: ../index.php') && exit() : header('');
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous">
        </script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <link href="../output.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/002afb9e14.js" crossorigin="anonymous"></script>
    <title>Sign up</title>
</head>

<body>
    <div class="flex flex-row justify-center items-center h-screen">
        <div class="grid grid-cols-1 p-10 md:p-16 rounded-lg bg-neutral md:w-7/12 xl:w-4/12">
            <form id="signupForm" class="form-control gap-2">
                <h1 class="text-center font-medium text-2xl uppercase">
                    Sign up
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
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="../login">
                    Already have an account?
                </a>
                <button type="submit" class="btn btn-primary my-4">
                    Sign up
                </button>
            </form>

        </div>
    </div>
</body>
<script>
    const [signupForm] = $('#signupForm');

    $(signupForm).on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '../api.php',
            type: 'POST',
            data: {
                signup: true,
                email: $('#email').val(),
                password: $('#password').val()
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.error) {
                    alert(data.error);
                } else {
                    alert(data.success);
                    window.location.href = '../login';
                }
            }
        })
    })
</script>

</html>