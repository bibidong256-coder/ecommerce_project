
// HAMBUGER MENU
const bar = document.getElementById("bar");
const close = document.getElementById("close");
const nav = document.getElementById("navbar");

// When hamburger (bar) is clicked → show the navbar
if (bar) {
    bar.addEventListener("click", () => {
        nav.classList.add("active");
    });
}

// When close (×) icon is clicked → hide the navbar
if (close) {
    close.addEventListener("click", () => {
        nav.classList.remove("active");
    });
}


if (bar && close) {

  close.style.display = 'none';

  // When the bar is clicked
  bar.addEventListener('click', () => {
    close.style.display = 'block'; 
    bar.style.display = 'none';   
  });

  // When the close button is clicked
  close.addEventListener('click', () => {
    close.style.display = 'none';  
    bar.style.display = 'block';   
  });
}


//CART
document.querySelector(".pro-container").addEventListener("click", function(e) {
    const link = e.target.closest(".cart-link");
    if (!link) return;  // click was not on a cart button, ignore

    e.preventDefault();

    const pro = link.closest(".pro");

    const id = new URL(
        pro.querySelector("a.view-details-btn").href
    ).searchParams.get("id");

    fetch("http://localhost/ecommerce/api/add_to_cart.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "product_id=" + id
    })
    .then(res => res.text())
    .then(data => {
        console.log(data);

        // Visual feedback
        const icon = link.querySelector("i");
        icon.classList.replace("fa-cart-shopping", "fa-check");
        setTimeout(() => {
            icon.classList.replace("fa-check", "fa-cart-shopping");
        }, 1000);
    });
});


//PHP STARTS HERE NOW

//step 4

const container = document.querySelector(".pro-container");
const page = parseInt(container.dataset.page) || 1;

fetch(`http://localhost/ecommerce/api/get_products.php?page=${page}`)
  .then(response => response.json())
  .then(result => {
    container.innerHTML = "";

    if (result.status === "empty" || !result.data || result.data.length === 0) {
      container.innerHTML = "<p>No products found on this page.</p>";
      return;
    }

    result.data.forEach(product => {
      container.innerHTML += `
        <div class="pro">
            <img src="shoes images/${product.image}" alt="${product.name}">
            <div class="des">
                <span>${product.brand}</span>
                <h5>${product.name}</h5>
                <div class="star">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <h4>Shs ${product.price}</h4>
            </div>
            <div class="product-actions">
                <a href="#" class="cart-link">
                    <i class="fa-solid fa-cart-shopping"></i>
                </a>
                <a href="product-details.php?id=${product.id}&from=${window.location.pathname.split('/').pop()}" class="view-details-btn">
                    <i class="fas fa-eye"></i> View Details
                </a>
            </div>
        </div>
      `;
    });
  })
  .catch(error => console.error("Error loading products:", error));

  //step 5


function addToCart(id) {
  fetch("http://localhost/ecommerce/api/add_to_cart.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "product_id=" + id
  })
  .then(res => res.text())
  .then(data => {
    alert("Product added to cart!");
    console.log(data);
  });
}

