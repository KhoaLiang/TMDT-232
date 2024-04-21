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

            $stmt = $conn->prepare('WITH RankedBooks AS (
  SELECT book.id, book.name,
         author.authorName,
         fileCopy.price AS filePrice,
         physicalCopy.price AS physicalPrice,
         book.imagePath AS pic,
         book.avgRating AS star,
         eventapply.eventID,
         COALESCE(eventdiscount.discount, 0) AS discount,
         ROW_NUMBER() OVER (PARTITION BY book.id ORDER BY discount DESC) AS discount_rank
  FROM book
  INNER JOIN author ON book.id = author.bookID
  INNER JOIN fileCopy ON book.id = fileCopy.id
  INNER JOIN physicalCopy ON book.id = physicalCopy.id
  LEFT JOIN eventapply ON book.id = eventapply.bookID
  LEFT JOIN eventdiscount ON eventapply.eventID = eventdiscount.ID
)
SELECT *
FROM RankedBooks
WHERE discount_rank = 1');
            // $stmt = $conn->prepare('select book.id, book.name, author.authorName, fileCopy.price as filePrice, physicalCopy.price as physicalPrice, book.imagePath as pic, book.avgRating as star from book inner join author on book.id = author.bookID
            // join fileCopy on book.id = fileCopy.id
            // join physicalCopy on book.id = physicalCopy.id');
            $stmt->execute();
            $result = $stmt->get_result();
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
            <link rel="stylesheet" href="/css/customer/book/book-list.css">
            <title>Book list</title>
            <style>
                  
            </style>
      </head>

      <body>
            <?php
            require_once __DIR__ . '/../../../layout/customer/header.php';
            ?>
            <section id="page">
            <h1 class="heading-decord" style="text-align: center;">Our collection</h1>

            <!--
            <ul id="book-List"
            <li>Example book</li>
            </ul>
            -->
      <div class="container-fluid w-75">
            <br>
            <!-- <div id="TestBookList">
                  <p>Test Item perpage here</p>
            </div> -->
            <div class="row  align-items-center">
                  <div class="col-12 col-lg-3 col-xl-2 border border-3 bg-light p-3 align-self-start rounded" style=" margin-top: 0px;">
                        <button type="button" class="btn-icon" data-bs-toggle="modal" data-bs-target="#exampleModal" id="modalToggleButton">
                              <i class="fas fa-filter"></i>
                              Filter
                        </button>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                              <div class="modal-dialog">
                                    <div class="modal-content">
                                          <div class="modal-header">
                                          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                          </div>
                                          <div class="modal-body">
                                                <ul class="Nav-header no-padding">
                                                      <li>Categories</li>
                                                </ul>
                                                <form class="d-flex search-form">
                                                      <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                                                </form>
                                                <ul class="Nav-list" id="Category-list">
                                                      <li>Fiction</li>
                                                      <li>Non-Fiction</li>
                                                      <li>Fantasy</li>
                                                      <li>Science Fiction</li>
                                                      <li>Horror</li>
                                                      <li>Thriller</li>

                                                </ul>



                                                <ul class="Nav-header no-padding">
                                                      <li>Publisher</li>
                                                </ul>
                                                <form class="d-flex search-form">
                                                            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                                                </form>
                                                <ul class="Nav-list">
                                                      <li>ABC</li>
                                                      <li>DEF</li>
                                                      <li>GHI</li>
                                                      <li>JKL</li>
                                                </ul>

                                                <ul class="Nav-header no-padding">
                                                      <li>Author</li>
                                                </ul>

                                                <ul class="Nav-list">
                                                      <li>Frank Herbert</li>
                                                      <li>Yuval Noah</li>
                                                      <li>Bram</li>
                                                </ul>
                                          </div>
                                          <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                          </div>
                                    </div>
                              </div>
                        </div>
                        <!-- Desktop Side pannel -->
                        <button class="btn btn-outline-dark" id="toggleButton">&#9776;</button>
                        <div id="hideable">
                              <ul class="Nav-header no-padding">
                                    <li>Categories</li>
                              </ul>
                              <form class="d-flex search-form">
                                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" id="Category-search">
                              </form>
                              <ul class="Nav-list" id="Category-list">
                                    <?php 
                                          if ($cate) {
                                                $success = $cate->execute();
                                                if ($success) {
                                                      $result1 = $cate->get_result();
                                                      while ($row = $result1->fetch_assoc()) {
                                                            // Process each row of data here...
                                                            echo '<li>'. $row['name'] . '</li>';
                                                      }
                                                            } else {
                                                      echo "Error executing statement: " . $conn->error;
                                                            }     
                                                      } else {
                                                      echo "Error preparing statement: " . $conn->error;
                                                      }
                                    ?>
                              </ul>

                              <ul class="Nav-header no-padding">
                                    <li>Publisher</li>
                              </ul>
                              <form class="d-flex search-form">
                                          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                              </form>
                              <ul class="Nav-list">
                                    <li>ABC</li>
                                    <li>DEF</li>
                                    <li>GHI</li>
                                    <li>JKL</li>
                              </ul>

                              <ul class="Nav-header no-padding">
                                    <li>Author</li>
                              </ul>

                              <ul class="Nav-list">
                                    <li>Frank Herbert</li>
                                    <li>Yuval Noah</li>
                                    <li>Bram</li>
                              </ul>
                        </div>
                  </div>
                  
                  <div class="col-12 col-lg-9 col-xl-9 border border-3 bg-light ms-xl-3 p-3 rounded">
                        <!-- begin search row -->
                        <div class="row">
                              <div class="col-12 col-md-6 m-2">
                                    <form class="d-flex align-items-center w-100 search_form mx-auto mx-lg-0 mt-2 mt-lg-0 order-2 order-lg-1 form-control me-2" role="search" id="search_book">
                                          
                                          <button type="submit" class="btn-icon">
                                                <i class="fas fa-search"></i>
                                          </button>
                                          <input id="search-input" class="form-control ms-2" type="search" placeholder="Search by name, author or ISBN number or Publisher" aria-label="Search">
                                          <!-- <input type="submit" value="Search" class="btn btn-primary"> -->
                                          
                                    </form>
                              </div>
                        <!-- end search row -->
                        </div>

                        <!-- begin category form and page nav -->
                        <div class="row">
                              <div class="col-12 col-md-3 m-2">
                                    <!-- category form -->
                                    <select class="form-select " aria-label="Default select example" id="DisplayBook">
                                          <option selected value="Default">Default Listing</option>
                                          <option value="Discount">Discount only</option>
                                          <option value="Best-Seller">Best Seller</option>
                                          <option value="HighToLowPhysical">Price Descending</option>
                                          <option value="LowToHighPhysical">Price Ascending</option>
                                          <option value="PublishDateDes">Pusblish Date Descending</option>
                                          <option value="PublishDateAsc">Pusblish Date Ascending</option>
                                    </select>  
                                    <!-- end of select discount and best seller form -->
                              </div>
                              <div class="col-12 col-md-2 m-2">
                                    <select class="form-select" id="itemsPerPage">
                                          <option value="100">All books</option>
                                          <option value="6" selected>6 books</option>
                                          <option value="12">12 books</option>
                                          <option value="24">24 books</option>
                                          <option value="48">48 books</option>
                                    </select>
                              </div>
                              <nav class=" col-12 col-md-1 m-2 page-nav" aria-label="Page navigation example">
                                    <ul class="pagination">
                                          <li class="page-item">
                                                <a class="page-link" href="#"><</a>
                                          </li>
                                          <!-- Add as many page links as you need -->
                                          <li class="page-item">
                                                <a class="page-link" href="#">></a>
                                          </li>
                                    </ul>
                              </nav> 
                        <!-- end page nav + items per page + sort by  -->
                        </div>
                        <hr>
                        <div id="bookList">
                              
                        </div>

                  </div>
            </div>
            
            
            <div class="row justify-content-center">
                  <nav class=" col-12 col-md-2 m-2 page-nav" aria-label="Page navigation example">
                        <ul class="pagination">
                              <li class="page-item">
                                    <a class="page-link" href="#"><</a>
                              </li>
                              <!-- Add as many page links as you need -->
                              <li class="page-item">
                                    <a class="page-link" href="#">></a>
                              </li>
                        </ul>
                  </nav> 
                  
            </div>
      </div>
            
        
            </section>
            <?php
            require_once __DIR__ . '/../../../layout/footer.php';
            ?>
            <script src="/javascript/customer/menu_after_load.js"></script>
            <script src="/tool/js/ratingStars.js"></script>
            <script src="/javascript/customer/book/book-list-cus.js"></script>
      </body>
      </html>

<?php } ?>