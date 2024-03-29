<?php
require_once __DIR__ . '/../../../tool/php/role_check.php';
require_once __DIR__ . '/../../../tool/php/ratingStars.php';

$return_status_code = return_navigate_error();

if ($return_status_code === 400) {
      http_response_code(400);
      require_once __DIR__ . '/../../../error/400.php';
} else if ($return_status_code === 403) {
      http_response_code(403);
      require_once __DIR__ . '/../../../error/403.php';
} else if ($return_status_code === 200) {
      require_once __DIR__ . '/../../../config/db_connection.php';
      require_once __DIR__ . '/../../../tool/php/converter.php';
      require_once __DIR__ . '/../../../tool/php/formatter.php';

      try {
            // Connect to MySQL
            $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

            // Check connection
            if (!$conn) {
                  http_response_code(500);
                  require_once __DIR__ . '/../../../error/500.php';
                  exit;
            }
            $elem = '';

            $stmt = $conn->prepare('select book.id, book.name, author.authorName, fileCopy.price as filePrice, physicalCopy.price as physicalPrice, book.imagePath as pic, book.avgRating as star from book inner join author on book.id = author.bookID
            join fileCopy on book.id = fileCopy.id
            join physicalCopy on book.id = physicalCopy.id');
            $stmt->execute();
            $result = $stmt->get_result();
            // $cate_re = $cate->get_result();
            // echo '<section id="page" class="container">';
            // while ($row = $result->fetch_assoc()) {
            //       $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['imagePath']));
            // echo '<div class="card" style="width: 18rem;">';
            // echo '<img src="' . $imagePath . '" class="card-img-top" alt="...">';
            // echo '<div class="card-body">';
            // echo '<h5 class="card-title">' . $row['name'] . '</h5>';
            // echo '<p class="card-text">Edition: ' . $row['edition'] . '</p>';
            // // Output other fields as needed...
            // echo '</div>';
            // echo '</div>';
            // }
            // echo '</section>';
            $cate = $conn->prepare('SELECT category.ID, category.name FROM category');
            $auth = $conn->prepare('SELECT author.authorName FROM author');
      } catch (Exception $e) {
            http_response_code(500);
            require_once __DIR__ . '/../../../error/500.php';
            exit;
      }
?>

      <!DOCTYPE html>
      <html lang="en">

      <head>
            <?php
            require_once __DIR__ . '/../../../head_element/cdn.php';
            require_once __DIR__ . '/../../../head_element/meta.php';
            ?>
            <link rel="stylesheet" href="/css/preset_style.css">
            <title>Book list</title>
            <style>
                  .grid-container {
                        display: grid;
                        grid-template-columns: auto auto auto auto;
                        justify-content: space-evenly;
                        align-content: center;
                  }
                  .card:hover {
                        transform: scale(1.1);
                  } 
                  .card {
                        margin: 1rem;
                  }
                  .author {
                        color: gray;
                  }
                  .pic {
                        height: 28rem;
                        width: 100%;
                  }
                  a{
                        text-decoration: none;
                        color: black;
                  }
                  @media (min-width: 767.98px) { .card-body {
                  max-height: 205px; /* Adjust this value as needed */
                  overflow: auto; /* Add a scrollbar if the content is too long */
                  } 
                  .card-body::-webkit-scrollbar {
                  display: none;
                  }
            }
            .heading-decord{
                  font-weight: bold;
                  padding: 20px;
            }
            </style>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/customer/header.php';
            ?>
            <section id="page">
            <h1 class="heading-decord" style="text-align: center;">Our collection</h1>
      <div class="container">
            <div class="row">
                  <div class="col-12 col-md-4 m-2">
                        <!-- category form -->
                  <select class="form-select " aria-label="Default select example" id="category">
                        <option selected>Category</option>
                        <?php 
                              if ($cate) {
                                    $success = $cate->execute();
                                    if ($success) {
                                          $result1 = $cate->get_result();
                                          while ($row = $result1->fetch_assoc()) {
                                                // Process each row of data here...
                                                echo '<option value="' . $row['ID'].'">'. $row['name'] . '</option>';
                                          }
                                                } else {
                                          echo "Error executing statement: " . $conn->error;
                                                }     
                                          } else {
                                          echo "Error preparing statement: " . $conn->error;
                                          }
                        ?>
                        </select>  
                  </div>
                  <button type="button" class="btn btn-outline-danger col-12 col-md-1 m-2">Discount</button>
                  <button type="button" class="btn btn-outline-warning col-12 col-md-1 m-2">Best seller</button>
                  <!-- search bar -->
                  <div class="col-12 col-md-5 m-2">
                   <form class="d-flex align-items-center w-100 search_form mx-auto mx-lg-0 mt-2 mt-lg-0 order-2 order-lg-1" role="search" id="search_form">
                                    <input id="search_book" class="form-control me-2" type="search" placeholder="Search by name, author or ISBN number" aria-label="Search">
                              </form>
                  </div>
            </div>
            <br>
            <div id="book-list">
            <?php
                  for ($i = 1; $i <= $result->num_rows; $i++) {
                  if ($i % 3 == 1) {
                        echo '<div class="row justify-content-center align-items-center g-2 m-3">';
                  }
                  echo '<div class="col-9 col-md-4">';
                  $row = $result->fetch_assoc();
                  // $row["pic"] = "src=\"https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row["pic"])) . "\"";
                  $imagePath = "https://{$_SERVER['HTTP_HOST']}/data/book/" . normalizeURL(rawurlencode($row['pic']));
                                                echo '<div class="card w-75 mx-auto d-block">';
                                                 echo "<a href=\"book-detail?bookID=".normalizeURL(rawurlencode($row["id"]))."\">"; 
                                                 echo '<img src="' . $imagePath . '" class="card-img-top" style="height: 28rem;" alt="...">';
                                                      echo "<div class=\"card-body\">";
                                                            echo "<h5 class=\"card-title\">"."Book: ".$row["name"]."</h5>";
                                                            echo "<p class=\"author\">".$row["authorName"]."</p>";
                                                            echo "<p class=\"price\">"."E-book price: ".$row["filePrice"]."$"."</p>";
                                                            echo "<p class=\"price\">"."Physical price: ".$row["physicalPrice"]."$"."</p>";
                                                            // $cnt = 1;
                                                            // $res="";
                                                            // while($cnt <= 5){
                                                            //       if ($cnt > $row["star"]){
                                                            //             if($cnt - $row["star"] > 0 && $cnt - $row["star"] < 1){
                                                            //                   $res .= "<i class=\"bi bi-star-half\"></i>";
                                                            //             }
                                                            //             else{
                                                            //                   $res .= "<i class=\"bi bi-star\"></i>";
                                                            //             }
                                                            //       }
                                                            //       else {
                                                            //             $res .= "<i class=\"bi bi-star-fill\"></i>";
                                                            //       }
                                                            //       $cnt++;
                                                            // }
                                                            echo '<span class="text-warning">'.displayRatingStars($row["star"]).'</span>';
                                                            echo "(".$row["star"].")";
                                                            
                                                      echo "</div>";
                                                echo "</a>";
                                                echo "</div>";

                  echo '</div>';
                  if ($i % 3 == 0 || $i == $result->num_rows) {
                        echo '</div>';
                  }
                  }
                  
                  
            ?>
            </div>
      
      </div>
            
        
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
      </body>

      </html>

<?php } ?>