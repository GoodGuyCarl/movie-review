<?php session_start();
require '../vendor/autoload.php';
$fb = new Facebook\Facebook([
     'app_id' => '6621295174664204',
     'app_secret' => 'f5801e183902397cb28ce980fae25af8',
     'default_graph_version' => 'v18.0',
]);

$helper = $fb->getRedirectLoginHelper();
if (isset($_SESSION['fb_access_token'])) {
     $logoutUrl = $helper->getLogoutUrl($_SESSION['fb_access_token'], 'https://localhost/movie-review/login');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Movies</title>
     <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous">
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
                    <a href="../" class="mx-10 text-lg font-thin">Movie Reviews</a>
               </div>
               <div class="navbar-start md:navbar-center relative">
                    <input id="searchInput" type="search" placeholder="Search movies.." class="input input-primary w-44 md:w-full text-inherit/50 pl-10" />
                    <span class="absolute flex items-center pl-3">
                         <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
               </div>
               <div class="navbar-end gap-3 sm:mr-5">
                    <?php if (isset($_SESSION['userid']) && !isset($_SESSION['fb_user_id'])) {
                         echo '<a onclick="logout()" class="btn btn-outline btn-primary"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>';
                    } else if (isset($_SESSION['fb_user_id'])) {
                         echo '<p class="text-sm font-thin">Hello, ' . $_SESSION['name'] . '</p>';
                         echo '<a href="../logout.php" class="btn
               btn-outline btn-primary"><i class="fa-solid fa-right-from-bracket"></i>Logout</a>';
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
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
               echo '<dialog id="addMovie" class="modal">
            <form id="movieForm" class="modal-box gap-5 flex flex-col drop-shadow">
                <h3 class="font-bold text-2xl">Create a movie</h3>
                <input placeholder="Movie name" type="text" class="input input-bordered input-primary w-full"
                    id="movie">
                <textarea class="textarea h-32 textarea-bordered textarea-primary w-full"
                    placeholder="Movie overview"></textarea>
                <div class="modal-action mt-0">
                    <div class="w-full justify-between flex">
                        <button formmethod="dialog" class="btn btn-sm md:btn-md">Cancel</button>
                        <button id="addMovieBtn" type="submit" formmethod="post"
                            class="btn btn-sm md:btn-md btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </dialog>';
          } ?>
          <div id="mainDiv" class="container mx-auto my-5 grid grid-cols-1">
               <div id="sectionTitle" class="flex mx-5 my-5 gap-5">
                    <h1 class="text-2xl font-bold">Available Movies</h1>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                         echo '<button class="btn btn-primary btn-sm" onclick="addMovie.showModal()">Add Movies</button>';
                    } ?>
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

</html>
<script>
     const star = `<div><i class="fa-solid fa-star"></i></div>`;
     const halfStar = `<div><i class="fa-solid fa-star-half-stroke"></i></div>`;
     const emptyStar = `<div><i class="fa-regular fa-star"></i></div>`;
     const [reviewForm, moviesCard, sectionTitle, main] = [$('#reviewForm'), $('#moviesCard'), $('#sectionTitle'), $(
          '#mainDiv')];
     const isLoggedIn = <?php echo isset($_SESSION['userid']) ? 'true' : 'false'; ?>

     $(document).ready(() => {
          loadMovies();
     })

     function loadMovies() {
          $.ajax({
               url: '../api.php',
               type: 'GET',
               data: {
                    getMovies: true
               },
               success: function(result) {
                    let movies = JSON.parse(result);
                    if (movies.error) {
                         moviesCard.empty();
                         sectionTitle.after(`
                    <div class="flex mx-5 my-5">
                        <p class="text-lg font-thin">${movies.error}</p>
                    </div>
                        `)
                    } else {
                         movies.forEach((movie) => {
                              $('#main').append(`
                                <dialog id="modal_${movie.id}" class="modal">
                                    <form id="reviewForm_${movie.id}" class="modal-box gap-5 flex flex-col drop-shadow">
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
                                                <button id="submitReviewBtn_${movie.id}" type="submit" formmethod="post" class="btn btn-sm md:btn-md btn-primary">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </dialog>
                                `)
                              $('#main').append(`
                        <dialog id="update_movie_${movie.id}" class="modal">
                            <form id="movieForm" class="modal-box gap-5 flex flex-col drop-shadow">
                                <h3 class="font-bold text-2xl">Update ${movie.title}</h3>
                                <input type="text" class="input input-bordered input-primary w-full" id="movie" value="${movie.title}">
                                <input type="hidden" id="movieId" value="${movie.id}">
                                <textarea id="updateTextArea_${movie.id}" class="textarea h-32 textarea-bordered textarea-primary w-full"
                                    placeholder="Movie overview"></textarea>
                                <div class="modal-action mt-0">
                                    <div class="w-full justify-between flex">
                                        <button formmethod="dialog" class="btn btn-sm md:btn-md">Cancel</button>
                                        <button id="updateMovieBtn_${movie.id}" type="submit" formmethod="post"
                                            class="btn btn-sm md:btn-md btn-primary">Update</button>
                                    </div>
                                </div>
                            </form>
                        </dialog>
                        `)
                              $('#updateTextArea_' + movie.id).val(movie.overview)
                              $('#updateMovieBtn_' + movie.id).click(function(e) {
                                   e.preventDefault();
                                   let form = $(this).closest('form');
                                   let movieId = form.find('#movieId').val();
                                   let newTitle = form.find('#movie').val();
                                   let newOverview = form.find('.textarea').val();
                                   $.ajax({
                                        url: '../api.php',
                                        type: 'post',
                                        data: {
                                             updateMovie: true,
                                             movieId: movieId,
                                             title: newTitle,
                                             overview: newOverview,
                                        },
                                        success: function(response) {
                                             let update = JSON.parse(
                                                  response);
                                             if (update.error) {
                                                  alert(update.error);
                                             }
                                             if (update.success) {
                                                  alert(update.success);
                                                  location.reload();
                                             }
                                        }
                                   })
                              })
                              $('#main').on('click', '#submitReviewBtn_' + movie.id, function(e) {
                                   e.preventDefault();
                                   let form = $(this).closest('form');
                                   let movie_id = form.find('#movie_id').val();
                                   let review = form.find('.textarea').val();
                                   let rating = form.find(
                                        'input[name="rating-2"]:checked').val();
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
                                        success: function(result) {
                                             let response = JSON.parse(
                                                  result);
                                             if (response.error) {
                                                  alert(response.error);
                                             } else {
                                                  alert(response.success);
                                                  location.reload();
                                             }
                                        },
                                        error: function(err) {
                                             alert(err);
                                        }
                                   });
                              });
                              moviesCard.append(`
                                <div class="card bg-neutral w-full max-w-full">
                                    <div class="card-body gap-5">
                                        <div class="card-title">
                                            <h5 class="text-md font-bold line-clamp-1">${movie.title}</h5>
                                            
                                        </div>
                                        <p class="text-sm line-clamp-3">${movie.overview}</p>
                                        <div class="card-actions flex-nowrap justify-between md:justify">
                                            <button class="btn btn-outline btn-xs sm:btn-sm lg:btn-md" onclick="viewMovieReviews(${movie.id})"><i class="fa-solid fa-eye"></i></button>
                                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'user') {
                                                  echo '<button class="btn btn-primary btn-xs sm:btn-sm lg:btn-md" onclick="modal_${movie.id}.showModal()">Review</button>';
                                             } ?>
                                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                                                  echo '
                                                    <div class="flex flex-row flex-nowrap space-evenly gap-2">
                                                    <button class="btn btn-outline btn-primary btn-xs sm:btn-sm lg:btn-md" onclick="update_movie_${movie.id}.showModal()"><i class="fa-solid fa-pen-to-square"></i></button>
                                                    <button class="btn btn-outline btn-secondary btn-xs sm:btn-sm lg:btn-md" onclick="deleteMovie(${movie.id})"><i class="fa-solid fa-trash"></i></button>
                                                    
                                                    </div>
                                                ';
                                             } ?>
                                        </div>
                                    </div>
                                </div>
                            `)
                         })
                    }
               },
               error: function(err) {
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
               success: function(response) {
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
               success: function(response) {
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
     setTimeout(function() {
          let isLoggedIn = sessionStorage.getItem('isLoggedIn') === 'true';
          if (isLoggedIn) {
               sessionExpire();
          }
     }, 30 * 60 * 1000);

     function viewMovieReviews(movieId) {
          $.ajax({
               url: '../api.php',
               type: 'get',
               data: {
                    getMovies: true,
                    id: movieId,
               },
               success: function(movieResponse) {
                    let movies = JSON.parse(movieResponse);
                    let moviesById = {};
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
                         success: function(response) {
                              const reviews = JSON.parse(response);
                              const main = $('#mainDiv');
                              main.append(
                                   `<div id="reviewsGrid" class="grid grid-cols-4 gap-5">`
                              )
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

     $('#addMovieBtn').on('click', function(e) {
          e.preventDefault();
          let form = $(this).closest('form');
          let movie_name = form.find('input').val();
          let movie_overview = form.find('textarea').val();
          $.ajax({
               url: '../api.php',
               type: 'post',
               data: {
                    createMovie: true,
                    movie_name: movie_name,
                    movie_overview: movie_overview
               },
               success: function(response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                         alert(data.success);
                         location.reload();
                    }
                    if (data.error) {
                         alert(data.error)
                    }
               }
          })
     })

     function deleteMovie(movieId) {
          $.ajax({
               url: '../api.php',
               type: 'post',
               data: {
                    deleteMovie: true,
                    movieId: movieId
               },
               success: function(response) {
                    let data = JSON.parse(response);
                    if (data.success) {
                         alert(data.success);
                         location.reload();
                    } else {
                         alert(data.error);
                    }
               }
          })
     }
     const debounceSearch = debounce(search, 500);

     function debounce(func, delay) {
          let debounceTimer;
          return function() {
               const context = this;
               const args = arguments;
               clearTimeout(debounceTimer);
               debounceTimer = setTimeout(() => func.apply(context, args), delay);
          };
     }
     $('#searchInput').on('keyup', function() {
          debounceSearch($(this).val());
     });

     function search(query) {
          $.ajax({
               method: 'get',
               url: '../api.php',
               data: {
                    searchMovies: true,
                    query: query,
               },
               success: function(response) {
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
                         } else {
                              main.append(
                                   `<div id="search" class="grid grid-cols-1 mx-10 gap-x-0 gap-y-5 md:grid-cols-2 md:place-items-center lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-2">`
                              )
                              let search = $('#search');
                              movies.forEach(function(movie) {
                                   search.append(`
                            <div class="card bg-neutral w-full max-w-full">
                                    <div class="card-body gap-5">
                                        <div class="card-title">
                                            <h5 class="text-md font-bold line-clamp-1">${movie.title}</h5>
                                            
                                        </div>
                                        <p class="text-sm line-clamp-3">${movie.overview}</p>
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
</script>