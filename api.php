<?php
require_once('vendor/autoload.php');
require 'database/dbconfig.php';
session_start();
$db = new DB();
$client = new \GuzzleHttp\Client();

if (isset($_SESSION['userid'])) {
    if ($_SESSION['expire'] <= time()) {
        session_destroy();
        echo json_encode(array('error' => 'Session expired, please login again.'));
    } else {
        $_SESSION['expire'] = time() + 30 * 60;
    }
}

if (isset($_GET['getPopularMovies'])) {
    $response = $client->request('GET', 'https://api.themoviedb.org/3/movie/popular?language=en-US&page=1', [
        'headers' => [
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiJhNzZkNDFhNDYxMjkxNWI5MzM4ODc3NWNiMmU4NDc1NCIsInN1YiI6IjYyZWI1OTRlNmQ5ZmU4MDA1ZWVkZWMwZiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.X-dJ5FQYwiUmdNGy2os8VPbb3MQl9FlApj7wi6dBsdE',
            'accept' => 'application/json',
        ],
    ]);
    echo $response->getBody();
}

if (isset($_GET['searchMovies'])) {
    $query = $_GET['query'];
    $db->select('movies', '*', "title LIKE '%$query%' OR overview LIKE '%$query%'");
    if (count($db->res) > 0) {
        echo json_encode($db->res);
    } else {
        echo json_encode(array('error' => 'This movie does not exist in the database.'));
    }
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $db->select('users', '*', "email='{$email}' AND password='{$password}'");
    if (count($db->res) > 0) {
        $_SESSION['userid'] = $db->res[0]['id'];
        $_SESSION['expire'] = time() + 30 * 60;
        echo json_encode($db->res[0]);
    } else {
        echo json_encode(array('error' => 'Invalid credentials'));
    }
}

if (isset($_POST['signup'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    try {
        $db->insert(
            'users',
            array(
                'email' => $email,
                'password' => $password
            )
        );
        echo json_encode(array('success' => 'User created successfully'));
    } catch (Exception $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}

if (isset($_GET['getMovies'])) {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $db->select('movies', '*', "id='{$id}'");
        if (count($db->res) > 0) {
            echo json_encode($db->res);
        } else {
            echo json_encode(array('error' => 'There are no movies with this id.'));
        }
    } else {
        $db->select('movies');
        if (count($db->res) > 0) {
            echo json_encode($db->res);
        } else {
            echo json_encode(array('error' => 'There are currently no movies in the database. Try again later.'));
        }
    }

}
if (isset($_POST['updateMovie'])) {
    $movie_id = $_POST['movieId'];
    $newTitle = $_POST['title'];
    $newOverview = $_POST['overview'];
    try {
        $db->update(
            'movies',
            array(
                'title' => $newTitle,
                'overview' => $newOverview
            ),
            "movie_id='{$movie_id}'"
        );
        echo json_encode(array('success' => 'Movie updated successfully'));
    } catch (Exception $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}

if (isset($_POST['deleteMovie'])) {
    $movie_id = $_POST['movieId'];
    try {
        $db->delete('movies', "movie_id='{$movie_id}'");
        echo json_encode(array('success' => 'Movie deleted successfully'));
    } catch (Exception $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}


if (isset($_GET['getReviews'])) {
    $db->select('reviews');
    if (count($db->res) > 0) {
        echo json_encode($db->res);
    } else {
        echo json_encode(array('error' => 'There are no reviews for any movies at the moment, go make one and be the first!'));
    }
}

if (isset($_POST['submitReview'])) {
    $user_id = $_POST['user_id'];
    $movie_id = $_POST['movie_id'];
    $review = $_POST['review'];
    $rating = $_POST['rating'];
    $movie = $_POST['movie'];
    try {
        $db->select('movies', '*', "title='{$movie}'");
        if (count($db->res) === 0) {
            echo json_encode(array('error' => 'The movie does not exist in our database.'));
        }
        if (count($db->res) > 0) {
            try {
                $db->insert(
                    'reviews',
                    array(
                        'user_id' => $user_id,
                        'movie_id' => $movie_id,
                        'review_text' => $review,
                        'review_rating' => $rating
                    )
                );
                echo json_encode(array('success' => 'Review submitted successfully'));
            } catch (Exception $e) {
                echo json_encode(array('error' => $e->getMessage()));
            }
        }
    } catch (Exception $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    if (isset($_POST['expire'])) {
        echo json_encode(array('success' => 'Session expired. Log in again.'));
    } else {
        echo json_encode(array('success' => 'Logged out successfully'));
    }
}

if (isset($_GET['getMovieReviews'])) {
    $movie_id = $_GET['movieId'];
    try {
        $db->select('reviews', '*', "movie_id={$movie_id}");
        if (count($db->res) === 0) {
            echo json_encode(array("error" => "There are no reviews for this movie."));
        }
        if (count($db->res) > 0) {
            echo json_encode($db->res);
        }
    } catch (Exception $e) {
        echo json_encode(array("error" => $e->getMessage()));
    }
}
