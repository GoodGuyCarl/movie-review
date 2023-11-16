<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies</title>
    <!-- <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous">
        </script> -->
    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" /> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script> -->
    <!-- <script src="https://kit.fontawesome.com/002afb9e14.js" crossorigin="anonymous"></script> -->
    <link href="output.css" rel="stylesheet">
    <script src="js/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/slick.css">
    <script src="js/slick.min.js"></script>
    <script src="js/002afb9e14.js"></script>
    <style>
        .slick-slide {
            margin: 0 20px;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">
    <nav>
        <div class="navbar bg-base-200">
            <div class="navbar-start">
                <a href="./" class="mx-10 text-lg font-thin">Brand name</a>
            </div>
            <div class="navbar-start md:navbar-center relative">
                <input id="searchInput" type="search" placeholder="Search movies.."
                    class="input input-primary w-44 md:w-full text-inherit/50 pl-10" />
                <span class="absolute flex items-center pl-3">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
            </div>
            <div class="navbar-end gap-3 sm:mr-5">
                <?php if (isset($_SESSION['userid'])) {
                    echo '<a onclick="logout()" class="btn btn-outline btn-primary"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>';
                } else {
                    echo '<div class="grid grid-cols-2 gap-2">
                    <a href="./login" class="btn btn-outline btn-ghost">Login</a>
                    <a href="./signup" class="btn btn-outline btn-primary">Sign up</a>
                </div>';
                } ?>
            </div>
        </div>
    </nav>
    <main>
        <div id="mainDiv" class="container mx-auto my-5 grid grid-cols-1">
            <div class="flex mx-5 my-5">
                <h1 class="text-2xl font-bold">Popular Today</h1>
            </div>
            <div id="carousel-popular"
                class="carousel carousel-center justify-center rounded-box gap-5 mx-5 drop-shadow-xl snap-x overflow-x-scroll">
                <div class="grid grid-cols-1 place-items-center gap-5">
                    <span class="loading loading-spinner loading-lg"></span>
                </div>
            </div>
        </div>
        <div class="container mx-auto my-5 grid grid-cols-1">
            <div class="flex mx-5 my-5">
                <h1 class="text-2xl font-bold">Recent Reviews</h1>
            </div>
            <div id="carousel-reviews"
                class="carousel carousel-center justify-center rounded-box gap-5 mx-5 drop-shadow-xl snap-x overflow-x-scroll">
                <div class="grid grid-cols-1 place-items-center gap-5">
                    <span class="loading loading-spinner loading-lg"></span>
                </div>
            </div>
            <div id="reviewGrid" class="grid grid-cols-1 place-items-center mt-5 gap-5">
                <a href="movies" class="btn btn-sm md:btn-md btn-primary">Explore Movies</a>
            </div>
        </div>
    </main>
    <footer class="footer footer-center p-4 bg-base-300 text-base-content mt-auto">
        <aside>
            <p class="font-thin">made by CLSU BSIT 4-2 students</p>
        </aside>
    </footer>
</body>

</html>
<script>
    $(document).ready(() => {
        loadPopular();
        loadReviews();
    });
    const [main, popularCarousel, reviewsCarousel, reviewGrid] = [$('#mainDiv'), $('#carousel-popular'), $('#carousel-reviews'), $('#reviewGrid')];
    const star = `<div><i class="fa-solid fa-star"></i></div>`;
    const halfStar = `<div><i class="fa-solid fa-star-half-stroke"></i></div>`;
    const emptyStar = `<div><i class="fa-regular fa-star"></i></div>`;
    const debounceSearch = debounce(search, 500);

    function debounce(func, delay) {
        let debounceTimer;
        return function () {
            const context = this;
            const args = arguments;
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(context, args), delay);
        };
    }
    $('#searchInput').on('keyup', function () {
        debounceSearch($(this).val());
    });
    function loadPopular() {
        $.ajax({
            method: 'get',
            url: 'api.php',
            data: {
                getPopularMovies: true
            },

            success: function (response) {
                let movies = JSON.parse(response);
                popularCarousel.empty();
                if (movies.error) {
                    popularCarousel.append(`<div>${movies.error}</div>`)
                }
                else {
                    movies.results.forEach(function (movie) {
                        popularCarousel.append(`
                         <div class="carousel-item relative">
                             <img class="h-96 object-cover" src="https://image.tmdb.org/t/p/w500${movie.poster_path}" alt="..."/>
                         </div>
                         `);
                    });
                    popularCarousel.slick({
                        vertical: false,
                        mobileFirst: true,
                        prevArrow: `<button>❮</button>`,
                        nextArrow: `<button>❯</button>`,
                        width: '100%',
                        variableWidth: true,
                        infinite: true,
                        autoplay: true,
                        autoplaySpeed: 5000,
                        centerMode: true,
                        responsive: [
                            {
                                breakpoint: 767,
                                settings: {
                                    slidesToShow: 3,
                                    slidesToScroll: 3,
                                    infinite: true,
                                }
                            },
                            {
                                breakpoint: 1023,
                                settings: {
                                    slidesToShow: 3,
                                    slidesToScroll: 3,
                                    infinite: true,
                                    autoplay: true,
                                    autoplaySpeed: 5000,
                                }
                            },
                            {
                                breakpoint: 1429,
                                settings: {
                                    slidesToShow: 5,
                                    slidesToScroll: 5,
                                    infinite: true,
                                    autoplay: true,
                                    autoplaySpeed: 5000,
                                    cssEase: 'ease-in',
                                    speed: 750,
                                    centerMode: false,
                                }
                            }
                        ],
                    });
                }
            }
        });
    }

    function loadReviews() {
        $.ajax({
            method: 'get',
            url: 'api.php',
            data: {
                getMovies: true
            },
            success: function (moviesResponse) {
                let movies = JSON.parse(moviesResponse);
                if (movies.error) {
                    reviewsCarousel.empty();
                    reviewsCarousel.append(`${movies.error}`)
                    console.error('Error fetching movies: ', movies.error);
                }
                if (movies.success) {
                    let moviesById = {};
                    movies.forEach(movie => {
                        moviesById[movie.id] = movie;
                    });
                }
                $.ajax({
                    method: 'get',
                    url: 'api.php',
                    data: {
                        getReviews: true
                    },
                    success: function (reviewsResponse) {
                        let reviews = JSON.parse(reviewsResponse);
                        if (reviews.error) {
                            reviewsCarousel.empty();
                            (<?php if (isset($_SESSION['userid'])) {
                                echo "true";
                            } else {
                                echo "false";
                            } ?> ? reviewGrid.prepend(`
                                <h1 class="text-md text-center font-thin mx-5 max-w-screen-sm md:text-lg">
                                    There are no reviews for any movies at the moment, go make one and be the first!
                                </h1>
                            `) : reviewGrid.prepend(`
                                <h1 class="text-md text-center font-thin mx-5 max-w-screen-sm md:text-lg">
                                    There are no reviews for any movies at the moment. Log in to make one!
                                </h1>
                            `))
                        }
                        else {
                            reviewsCarousel.empty();
                            reviews.forEach(review => {
                                let movie = moviesById[review.movie_id];
                                if (movie) {
                                    reviewsCarousel.append(`
                                        <div class="card bg-neutral">
                                            <div class="card-body">
                                                <h1 class="card-title">${movie.title}</h1>
                                                <div class="flex flex-row">
                                                    ${star.repeat(review.review_rating)}
                                                    ${review.review_rating % 1 !== 0 ? halfStar : ''}
                                                    ${emptyStar.repeat(5 - review.review_rating - (review.review_rating % 1 !== 0 ? 0.5 : 0))}
                                                </div>
                                                <p class="text-sm text-neutral-500 max-h-16 line-clamp-2">
                                                    ${review.review_text}
                                                </p>
                                            </div>
                                        </div>`
                                    );
                                }
                                else {
                                    console.error(`No movie found for review with movie id ${review.movie_id}`);
                                }
                            });
                            reviewsCarousel.slick({
                                vertical: false,
                                prevArrow: `<button>❮</button>`,
                                nextArrow: `<button>❯</button>`,
                                width: '100%',
                                variableWidth: true,
                                slidesToShow: 4,
                                slidesToScroll: 4,
                            })
                        }
                    }
                })
            }
        })
    }
    function viewMovieReviews(movieId) {
        $.ajax({
            url: './api.php',
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
                    url: './api.php',
                    type: 'get',
                    data: {
                        getMovieReviews: true,
                        movieId: movieId,
                    },
                    success: function (response) {
                        const reviews = JSON.parse(response);
                        console.log(reviews);
                        const main = $('#mainDiv');
                        main.append(`<div id="reviewsGrid" class="grid grid-cols-4 gap-5 mx-auto">`)
                        const reviewGrid = $('#reviewsGrid');
                        if (reviews.error) {
                            main.append(`
                            <div class="mx-auto">
                                ${reviews.error}
                            </div>
                            `)
                        } else {
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
                        }
                        reviewGrid.append(`</div>`)
                    }
                })
            }
        })
    }

    function search(query) {
        $.ajax({
            method: 'get',
            url: 'api.php',
            data: {
                searchMovies: true,
                query: query,
            },
            success: function (response) {
                if (query === '') {
                    return false;
                } else {
                    let movies = JSON.parse(response);
                    main.empty();
                    main.append(`
                    <div class="grid grid-cols-1 justify-center gap-5">
                        <h1 class="text-2xl font-bold text-center mb-5">Search Results</h1>
                    </div>
                    `);
                    if (movies.error) {
                        main.append(`<div class="font-thin mx-auto">
                        ${movies.error}
                        </div>`);
                    }
                    else {
                        main.append(`<div id="search" class="grid grid-cols-1 mx-10 gap-x-0 gap-y-5 md:grid-cols-2 md:place-items-center lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-2">`)
                        let search = $('#search');
                        movies.forEach(function (movie) {
                            search.append(`
                            <div class="card bg-neutral min-h-16 w-full md:w-72">
                                <div class="card-body gap-5">
                                    <div class="card-title line-clamp-1">${movie.title}</div>
                                    <p class="text-sm text-neutral-500 max-h-16 line-clamp-2">
                                        ${movie.overview}
                                    </p>
                                    <div class="card-actions flex-nowrap justify-between md:justify">
                                        <button class="btn btn-outline btn-xs sm:btn-sm lg:btn-md" onclick="viewMovieReviews(${movie.id})"><i class="fa-solid fa-eye"></i></button>
                                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'user') {
                                            echo '<button class="btn btn-primary btn-xs sm:btn-sm lg:btn-md" onclick="modal_${movie.id}.showModal()">Review</button>';
                                        } ?>
                                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                                            echo '<button class="btn btn-outline btn-secondary btn-xs sm:btn-sm lg:btn-md" onclick="deleteMovie(${movie.id})"><i class="fa-solid fa-trash"></i></button>';
                                        } ?>
                                    </div>
                                </div>
                            </div>
                            `)
                        });
                    }
                    main.append(`</div>`)
                }
            },
        });
        return false;
    }
    function logout() {
        $.ajax({
            url: 'api.php',
            type: 'post',
            data: {
                logout: true,
            },
            success: function (response) {
                sessionStorage.setItem('isLoggedIn', 'false');
                const res = JSON.parse(response);
                if (res.success) {
                    window.location.href = './login';
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
</script>