<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies</title>
    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous">
        </script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <link href="../output.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/002afb9e14.js" crossorigin="anonymous"></script>
</head>

<body class="min-h-screen flex flex-col">
    <nav>
        <div class="navbar bg-base-200">
            <div class="navbar-start">
                <a href="../" class="mx-10 text-lg font-thin">Brand name</a>
            </div>
            <!--<div class="navbar-start md:navbar-center relative">
            <input id="searchInput" type="search" placeholder="Search movies.."
                   class="input input-primary w-44 md:w-full text-inherit/50 pl-10"/>
            <span class="absolute flex items-center pl-3">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
        </div>-->
            <div class="navbar-end gap-3 sm:mr-5">
                <?php if (isset($_SESSION['userid'])) {
                    echo '<div class="dropdown dropdown-bottom">
                    <label tabindex="0" class="btn-sm rounded-btn cursor-pointer">
                        Hello, User!
                        <i class="fa-regular fa-face-smile"></i>
                    </label>
                    <ul class="dropdown-content z-[1] menu p-2 drop-shadow bg-base-200 rounded-box w-28" tabindex="0">
                        <li><a onclick="logout()"><i class="fa-solid fa-right-from-bracket"></i>Logout</a></li>
                    </ul>
                </div>';
                } else {
                    echo '<div class="grid grid-cols-2 gap-2">
                    <a href="../login" class="btn btn-outline btn-ghost">Login</a>
                    <a href="../signup" class="btn btn-outline btn-primary">Sign up</a>
                </div>';
                } ?>
            </div>
        </div>
    </nav>
    <main id="main">
        <div id="mainDiv" class="container mx-auto my-5 grid grid-cols-1">
            <div id="sectionTitle" class="flex mx-5 my-5">
                <h1 class="text-2xl font-bold">Available Movies</h1>
            </div>
            <div id="moviesCard" class="grid grid-cols-2 gap-2 mx-2.5 md:grid-cols-3 lg:grid-cols-4">
            </div>
        </div>

    </main>
    <footer class="footer footer-center p-4 bg-base-300 text-base-content mt-auto">
        <aside>
            <p class="font-thin">made by CLSU BSIT 4-2 students</p>
        </aside>
    </footer>
</body>
<script>
    const star = `<div><i class="fa-solid fa-star"></i></div>`;
    const halfStar = `<div><i class="fa-solid fa-star-half-stroke"></i></div>`;
    const emptyStar = `<div><i class="fa-regular fa-star"></i></div>`;

    $(document).ready(() => {
        loadMovies();
    })


    const reviewForm = $('#reviewForm');
    function loadMovies() {
        $.ajax({
            url: '../api.php',
            type: 'GET',
            data: {
                getMovies: true
            },
            success: function (result) {
                let movies = JSON.parse(result);
                let moviesCard = $('#moviesCard');
                let sectionTitle = $('#sectionTitle');
                if (movies.error) {
                    moviesCard.empty();
                    sectionTitle.after(`
                    <div class="flex mx-5 my-5">
                        <p class="text-lg font-thin">${movies.error}</p>
                    </div>
                        `)
                }
                else {
                    console.log(movies)
                    movies.forEach((movie) => {
                        $('#main').append(`
                                <dialog id="modal_${movie.id}" class="modal">
                                    <form id="reviewForm" class="modal-box gap-5 flex flex-col drop-shadow">
                                        <h3 class="font-bold text-2xl">Create a review</h3>
                                        <input placeholder="Movie" type="text" class="input input-bordered input-primary w-full" id="movie" value="${movie.title}" disabled>
                                        <input hidden type="text" class="input input-bordered input-primary w-full" id="movie_id" value="${movie.id}">
                                        <div class="rating">
                                            <div class="rating">
                                                <input type="radio" name="rating-2" class="mask mask-star-2 bg-orange-400" value="1" checked/>
                                                <input type="radio" name="rating-2" class="mask mask-star-2 bg-orange-400" value="2" />
                                                <input type="radio" name="rating-2" class="mask mask-star-2 bg-orange-400" value="3" />
                                                <input type="radio" name="rating-2" class="mask mask-star-2 bg-orange-400" value="4" />
                                                <input type="radio" name="rating-2" class="mask mask-star-2 bg-orange-400" value="5" />
                                            </div>
                                        </div>
                                        <textarea class="textarea h-32 textarea-bordered textarea-primary w-full" placeholder="Write your review here..."></textarea>
                                        <div class="modal-action mt-0">
                                            <div class="w-full justify-between flex">
                                                <button formmethod="dialog" class="btn btn-sm md:btn-md">Cancel</button>
                                                <button type="submit" formmethod="post" class="btn btn-sm md:btn-md btn-primary submitBtn">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </dialog>`)
                        $('.submitBtn').click(function (e) {
                            e.preventDefault();
                            let form = $(this).closest('form');
                            let movie_id = form.find('#movie_id').val();
                            let review = form.find('.textarea').val();
                            let rating = form.find('input[name="rating-2"]:checked').val();
                            let movie = form.find('#movie').val();
                            $.ajax({
                                url: '../api.php',
                                type: 'POST',
                                data: {
                                    submitReview: true,
                                    user_id: <?php if (isset($_SESSION['userid'])) {
                                        echo $_SESSION['userid'];
                                    } else {
                                        echo 0;
                                    }
                                    ?>,
                                    movie_id: movie_id,
                                    review: review,
                                    rating: rating,
                                    movie: movie
                                },
                                success: function (result) {
                                    let response = JSON.parse(result);
                                    if (response.error) {
                                        alert(response.error);
                                    }
                                    else {
                                        alert(response.success);
                                    }
                                },
                                error: function (err) {
                                    alert(err);
                                }
                            });
                        });
                        moviesCard.append(`
                                <div class="card bg-neutral w-full max-w-full">
                                    <div class="card-body">
                                        <div class="card-title">
                                            <h5 class="text-md font-bold line-clamp-1">${movie.title}</h5>
                                            
                                        </div>
                                        <p class="text-sm line-clamp-3">${movie.overview}</p>
                                        <div class="card-actions flex-nowrap justify-between">
                                            <button class="btn btn-outline btn-xs sm:btn-sm lg:btn-md" onclick="viewMovieReviews(${movie.id})">View</button>
                                            <?php if (isset($_SESSION['userid'])) {
                                                echo '<button class="btn btn-primary btn-xs sm:btn-sm lg:btn-md" onclick="modal_${movie.id}.showModal()">Review</button>';
                                            } ?>
                                        </div>
                                    </div>
                                </div>
                            `)
                    })
                }
            },
            error: function (err) {
                alert(err);
            }
        })
    }
    function logout() {
        $.ajax({
            url: '../api.php',
            type: 'post',
            data: {
                logout: true,
            },
            success: function (response) {
                const res = JSON.parse(response);
                if (res.success) {
                    window.location.href = '../login';
                } else {
                    alert(res.error);
                }
            }
        });
    }

    function sessionExpire() {
        $.ajax({
            url: 'api.php',
            type: 'post',
            data: {
                logout: true,
                expire: true
            },
            success: function (response) {
                const res = JSON.parse(response);
                if (res.success) {
                    alert(res.success)
                    window.location.href = './login';
                } else {
                    alert(res.error);
                }
            }
        });
    }
    setTimeout(function () {
        sessionExpire();
    }, 30 * 60 * 1000);

    function viewMovieReviews(movieId) {
        $.ajax({
            url: '../api.php',
            type: 'get',
            data: {
                getMovies: true,
                id: movieId,
            },
            success: function (movieResponse) {
                let movies = JSON.parse(movieResponse);
                let moviesById = {};
                const main = $('#mainDiv');
                main.empty();
                movies.forEach(movie => {
                    moviesById[movie.id] = movie;
                    main.append(`
                        <div id="sectionTitle" class="flex mx-5 my-5">
                            <h1 class="text-2xl font-bold">${movie.title}</h1>
                        </div>
                    `)
                });
                $.ajax({
                    url: '../api.php',
                    type: 'get',
                    data: {
                        getMovieReviews: true,
                        movieId: movieId,
                    },
                    success: function (response) {
                        const reviews = JSON.parse(response);
                        const main = $('#mainDiv');
                        main.append(`<div id="reviewsGrid" class="grid grid-cols-4 gap-5 mx-auto">`)
                        const reviewGrid = $('#reviewsGrid');
                        reviews.forEach((review) => {
                            let movie = moviesById[review.movie_id];
                            if (movie) {
                                reviewGrid.append(`
                                    <div class="bg-base-200 p-5 rounded-lg">
                                        <div class="flex flex-row">
                                            ${star.repeat(review.review_rating)}
                                            ${review.review_rating % 1 !== 0 ? halfStar : ''}
                                            ${emptyStar.repeat(5 - review.review_rating - (review.review_rating % 1 !== 0 ? 0.5 : 0))}
                                        </div>
                                        <div>
                                            ${review.review_text}
                                        </div>
                                    </div>
                                `)
                            }
                        })
                        reviewGrid.append(`</div>`)
                    }
                })
            }
        })
    }

</script>

</html>