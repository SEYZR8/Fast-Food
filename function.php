<?php
session_start();

function headers()
{
include "database/conf.php";
    echo ' 
    <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title> Food Website </title>

            <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
        <!-- font awesome cdn link  -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" >
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/waitme@1.19.0/waitMe.min.css">
        <!-- custom css file link  -->
        <link rel="stylesheet" href="admin/css/-variables.css">
    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/inv.css">

    <link rel="stylesheet" href="css/style1.css">
    <link rel="stylesheet" href="css/login.css">
    

        
    <!-- header section starts  -->
        </head>
        <body>
    <header class="d-flex">
    
        <a href="#" class="logo"><i class="fas fa-utensils "></i>resto.</a>
    
        <nav class="navbar">
            <a class="active" href="#home">home</a>
            <a href="#dishes">dishes</a>
            <a href="#about">about</a>
            <a href="#menu">menu</a>
            
            <a href="#order">order</a>
            <a href="admin/index.php" id="adminLink">Admin Panel</a>
                 </nav>
    
        <div class="icons">
            <i class="fas fa-bars" id="menu-bars"></i>
     
                <a class="fas fa-search" id="search-icon"></a>
                <a class="dropdown fas fa-tint" data-dropdown="#color-gallery">
                
                            <div class="dropdown-menu" id="color-gallery">';
                            $q=$conn->query("SELECT * FROM `colors`  ORDER  by clr_sts ASC") or die("<a>No color found</a>");
                            if($q){
                                while($row = mysqli_fetch_assoc($q)){
                                echo  '<a class="dropdown-item color-item '.$row["clr_sts"].' "  data-color-sts = "'.$row["clr_sts"].'" data-color="'.$row["clr"].'"; data-hsl="'.$row["hsl"].'" data-color-alt="'.$row["color_alt"]
                                .'" data-color-lighter="'.$row["color_lighter"].'" data-hsl="340" style="--clr:'.$row["clr"].';" href="#">
                                        </a>';
                            }
                            } 
                            
                        echo' </div>
                        </a>
                   

                
                                           <a  id="CartCount" class="fas fa-shopping-cart cart_show"></a>
                            
                        </div>
                    <div class="user" id="user_login_header"></div>
                        
                    </div>

    </header>
    
    <!-- header section ends-->
    
    <!-- search form  -->
    ';
    search();
};
function banners()
{


    echo ' <!-- home section starts  -->
            <section class="home" id="home">
            
                <div class="swiper-container home-slider " style="overflow-x:hidden ;">
            
                    <div id="banner_Container" class="swiper-wrapper wrapper" >
            
            
                    </div>
            
                    <div class="swiper-pagination"></div>
            
                </div>
            
            </section>
            
            <!-- home section ends -->
            ';
};
function search()
{
    echo ' <form id="search-form">
    <div class="inner-form">
      <div class="close_btn">X</div>
      <div class="basic-search">
        <div class="input-field">
          <div class="">
            <input
              id="SearchInput"
              type="text"
              class="text-white"
              placeholder="Search..."
             
            />
            <button class="search-btn">search</button>
          </div>
        </div>
        <div class="search-term d-none">
          <ul class="">
            <li>search</li>
            <li>search</li>
            <li>search</li>
            <li>search</li>
            <li>search</li>
            <li>search</li>
            <li>search</li>
          </ul>
        </div>
      </div>
    </div>
  </form>
    ';
}
function msgModals(){
    echo '

    <div class="modal fade " id="MsgModel" tabindex="1062" role="dialog">
        <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                  <h5 class="modal-title"></h5>
                  <a type="button" class="  close " data-dismiss="modal" aria-label="Close">
                      <span class="dpanel-text">X</span>
                  </a>
                  </div>
                  <div class="modal-body">
                  <p id="Model_txt"></p>
                  </div>
                  <div class="modal-footer">
                  
                  <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                  </div>
              </div>
        </div>
    </div>';
}
function search_result()
{
    echo '
    <section class="dishes d-none" id="search_main_container">
         <div class="title-head">
         
        <h3 class="sub-heading"> your search data </h3>
        <h1 class="heading"> search dishes </h1>
        </div>   

            <div id="search_msg"></div>
            <div class="box-container" id="search_containers">
                    <div class="row" id="search_gallery">
                    </div>
            </div>
    </section>
    ';
}
function loadtabel()
{
    echo '<div class="container mt-5">
    <div class="table-responsive " id="cart_tabel">
        <table  class="table table-striped-columns
        ">
        <thead class="tabel-info bg ">
        
                <tr class="bg text-white">
                    <th>Sno:</th>
                    <th>image</th>
                    <th>title</th>
                    <th>qty</th>
                    <th>prize</th>
                    <th>Total prize</th>
                    <th><button class="btn btn-danger" id="delete_all"> Delete All</button> </th>
                    </tr>
                    </thead>
                    <tbody class="" id="cart_data_show">
                    
                    <caption class="w-100">
                    <span  class= "ml-5">   Grand total : <b  id="crt_amt"></b> </span>
                     <button role="button" id="crt_inv_shw_btn"  class=" mr-5 float-end btn "> buying</button>
                     </caption>
                </tbody>
         </table>
    </div>
    
    </div>';
}

function dishes()
{

    echo '

    <!-- dishes section starts  -->
    
    <section class="dishes" id="dishes">
    <div class="title-head">
        <h3 class="sub-heading"> our dishes </h3>
        <h1 class="heading"> popular dishes </h1>
        </div>
    
        <div  id="p_msg"></div>
        <div class="box-container" id="dishes_containers">
                <div class="row" id="product_gallery">
                

                </div>
        </div>
    
    </section>
    
    <!-- dishes section ends ----->';
};
function review()
{
    include 'database/conf.php';
    echo '
    <!-- review section starts  -->
    
    <section class="review" id="review">
    <div class="title-head">
        <h3 class="sub-heading"> customer review </h3>
        <h1 class="heading"> what they say </h1>
    </div>
        <div class="swiper-container review-slider">
    
            <div class="swiper-wrapper">';
    
    $q=$conn->query("SELECT fb.msg ,fb.date , r.Name,r.image FROM `feedback` as fb JOIN register as r on fb.user_id = r.unique_id");
    if(mysqli_num_rows($q)>0){
        while($row = mysqli_fetch_assoc($q)){

        
    echo'<div class="swiper-slide slide">
                    <i class="fas fa-quote-right"></i>
                    <div class="user">
                        <img src="database/upload/'.$row["image"].'" alt="">
                        <div class="user-info">
                            <h3>'.$row["Name"].'</h3>
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <br>
                                   <span>'.$row["date"].'</span>
                                   
                            </div>
                        </div>
                    </div>
                    <p>'.$row["msg"].'</p>
                </div>';
                                }
        }
            echo'</div>
    
        </div>
        
    </section>
    
    <!-- review section ends -->
    ';
};
function introduction()
{
    echo '<!-- about section starts  -->

            <section class="about" id="about">
            <div class="title-head">
                <h3 class="sub-heading"> about us </h3>
                <h1 class="heading"> why choose us? </h1>
            </div>
                <div class="row">

                    <div class="image">
                        <img src="images/about-img.png" alt="">
                    </div>

                    <div class="content">
                        <h3>best food in the country</h3>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore, sequi corrupti corporis quaerat voluptatem ipsam neque labore modi autem, saepe numquam quod reprehenderit rem? Tempora aut soluta odio corporis nihil!</p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aperiam, nemo. Sit porro illo eos cumque deleniti iste alias, eum natus.</p>
                        <div class="icons-container">
                       
                        <div class="icons">
                                <i class="fas fa-shipping-fast"></i>
                                <span>free delivery</span>
                            </div>
                            <div class="icons">
                                <i class="fas fa-dollar-sign"></i>
                                <span>easy payments</span>
                            </div>
                            <div class="icons">
                                <i class="fas fa-headset"></i>
                                <span>24/7 service</span>
                            </div>
                        </div>
                        <a href="#" class="btn">learn more</a>
                    </div>

                </div>

            </section>

            <!-- about section ends -->
            ';
};
function special_menu()
{
    echo '
            <!-- menu section starts  -->

            <section class="menu" id="menu">
            <div class="title-head">
                <h3 class="sub-heading"> our menu </h3>
                <h1 class="heading"> today`s speciality </h1>
                </div>
            
                <div id="weekly_msg"></div>
                <div class="box-container" id="menu_container">
                <div class="row" id="WeeklyProGall">
                

                </div>

                </div>

            </section>

            <!-- menu section ends -->
            ';


};
function order_contact()
{
    echo '<!-- order section starts  -->

<section class="order" id="order">

    <div class="title-head">
        <h3 class="sub-heading"> order now </h3>
        <h1 class="heading"> fast and free </h1>
    </div>

    <div id="order_msg"></div>

    <form id="orderForm" action="database/DirectOrder.php" method="post">

        <div class="inputBox">
            <div class="input">
                <span>your name</span>
                <input type="text" name="name" placeholder="enter your name">
            </div>
            <div class="input">
                <span>your number</span>
                <input type="number" name="number" placeholder="enter your number">
            </div>
        </div>
        <div class="inputBox">
            <div class="input">
                <span>your order</span>
                <input type="text" name="order" placeholder="enter food name">
            </div>
            <div class="input">
                <span>additional food</span>
                <input type="text" name="additional" placeholder="extra with food">
            </div>
        </div>
        <div class="inputBox">
            <div class="input">
                <span>how musch</span>
                <input type="number" name="quantity" placeholder="how many orders">
            </div>
            <div class="input">
                <span>date and time</span>
                <input type="datetime-local" name="date" >
            </div>
        </div>
        <div class="inputBox">
            <div class="input">
                <span>your address</span>
                <textarea name="address" placeholder="enter your address" id="" cols="30" rows="10"></textarea>
            </div>
            <div class="input">
                <span>your message</span>
                <textarea name="message" placeholder="enter your message" id="" cols="30" rows="10"></textarea>
            </div>
        </div>

        <input type="submit" value="order now" class="btn">

    </form>

</section>

<!-- order section ends -->';
};
function footers()
{
    echo '<!-- footer section starts  -->

<section class="footer">

    <div class="box-container">

        <div class="box">
            <h3>locations</h3>
            <a href="#">india</a>
            <a href="#">japan</a>
            <a href="#">russia</a>
            <a href="#">USA</a>
            <a href="#">france</a>
        </div>

        <div class="box">
            <h3>quick links</h3>
            <a href="#home">home</a>
            <a href="#dishes">dishes</a>
            <a href="#about">about</a>
            <a href="#menu">menu</a>
            <a href="#review">review</a>
            <a href="#order">order</a>
        </div>

        <div class="box">
            <h3>contact info</h3>
            <a href="#">+123-456-7890</a>
            <a href="#">+111-222-3333</a>
            <a href="#">example@gmail.com</a>
            <a href="#">example@gmail.com</a>
            <a href="#">mumbai, india - 400104</a>
        </div>

        <div class="box">
            <h3>follow us</h3>
            <a href="#">facebook</a>
            <a href="#">twitter</a>
            <a href="#">instagram</a>
            <a href="#">linkedin</a>
        </div>

    </div>

    <div class="credit"> copyright @ 2023 by <span>mr. web designer</span> </div>

</section>

<!-- footer section ends -->

<!-- loader part  -->

<div class="loader-container">
    <img src="images/loader.gif" alt="">
</div>


<!-- custom js file link  -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="js/jquery.min.js"></script>
<script src="js/script.js"></script>
<script src="js/fetch.js"></script>

</body>
</html>';
}
