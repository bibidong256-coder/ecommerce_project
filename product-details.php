<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Product Details</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: "Poppins", sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f7f7f7;
    }
    .product-container {
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
      max-width: 1000px;
      margin: 4rem auto;
      background: #fff;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .product-image {
      flex: 1 1 400px;
    }
    .main-image {
      width: 100%;
      border-radius: 12px;
      cursor: zoom-in;
      transition: transform 0.3s ease;
    }
    .main-image:hover {
      transform: scale(1.01);
    }
    .thumbnail-gallery {
      display: flex;
      gap: 12px;
      margin-top: 15px;
      justify-content: center;
    }
    .thumbnail {
      width: 80px;
      height: 80px;
      border-radius: 8px;
      object-fit: cover;
      cursor: pointer;
      border: 2px solid transparent;
      transition: all 0.3s ease;
      opacity: 0.8;
    }
    .thumbnail:hover {
      opacity: 1;
      transform: translateY(-3px);
    }
    .thumbnail.active {
      border-color: #f7b731;
      opacity: 1;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .product-info {
      flex: 1 1 400px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .product-info h2 {
      font-size: 1.8rem;
      margin-bottom: 0.5rem;
    }
    .product-info span.brand {
      color: #555;
      text-transform: uppercase;
      font-size: 0.9rem;
      letter-spacing: 1px;
    }
    .product-info .price {
      font-size: 1.4rem;
      font-weight: bold;
      color: #222;
      margin: 1rem 0;
    }
    .stars {
      color: #f7b731;
      margin: 0.5rem 0;
    }
    .description {
      color: #444;
      line-height: 1.5;
      margin-bottom: 1.5rem;
    }
    .btn {
      background: #222;
      color: #fff;
      border: none;
      padding: 0.8rem 1.5rem;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
      transition: 0.3s;
      width: fit-content;
    }
    .btn:hover:not(:disabled) {
      background: #f7b731;
      color: #000;
    }
    .btn:disabled {
      opacity: 0.7;
      cursor: not-allowed;
    }
    .back {
      display: inline-flex;
      align-items: center;
      text-decoration: none;
      font-size: 16px;
      font-weight: 600;
      color: #007bff;
      margin-top: 20px;
      padding: 5px 0;
      transition: color 0.3s ease, transform 0.3s ease;
    }
    .back:hover {
      color: #0056b3;
      transform: translateX(-3px);
    }
    /* Toast */
    #toast {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: #088178;
      color: #fff;
      padding: 12px 22px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      opacity: 0;
      transform: translateY(10px);
      transition: all 0.3s;
      pointer-events: none;
      z-index: 9999;
    }
    #toast.show {
      opacity: 1;
      transform: translateY(0);
    }
    /* Loading state */
    .loading {
      width: 100%;
      text-align: center;
      padding: 3rem;
      color: #888;
    }
    .loading i {
      font-size: 2rem;
      margin-bottom: 1rem;
      display: block;
    }
    /* Error state */
    .error-state {
      width: 100%;
      text-align: center;
      padding: 3rem;
      color: #e74c3c;
    }
    /* Responsive */
    @media (max-width: 768px) {
      .product-container {
        margin: 2rem 1rem;
        padding: 1.5rem;
      }
      .thumbnail-gallery { gap: 8px; }
      .thumbnail { width: 60px; height: 60px; }
    }
  </style>
</head>
<body>

  <div class="product-container" id="product-details"></div>
  <div id="toast"></div>

  <script>
    const params    = new URLSearchParams(window.location.search);
    const productId = parseInt(params.get("id"));
    const container = document.getElementById("product-details");
    const fromPage  = params.get("from") || "shop.php";

    // ── Toast helper ──────────────────────────────────────────────────────────
    function showToast(msg, isError = false) {
      const t = document.getElementById('toast');
      t.textContent    = msg;
      t.style.background = isError ? '#e74c3c' : '#088178';
      t.classList.add('show');
      setTimeout(() => t.classList.remove('show'), 2500);
    }

    if (!productId) {
      container.innerHTML = `
        <div class="error-state">
          <i class="fas fa-exclamation-circle"></i>
          <p>No product specified.</p>
          <a href="${fromPage}" class="back">← Back to Shop</a>
        </div>
      `;
    } else {

      container.innerHTML = `
        <div class="loading">
          <i class="fas fa-spinner fa-spin"></i>
          <p>Loading product...</p>
        </div>
      `;

      fetch(`http://localhost/ecommerce/api/get_single_product.php?id=${productId}`)
        .then(res => res.json())
        .then(result => {

          if (result.status !== "success") {
            container.innerHTML = `
              <div class="error-state">
                <i class="fas fa-box-open"></i>
                <p>Product not found.</p>
                <a href="${fromPage}" class="back">← Back to Shop</a>
              </div>
            `;
            return;
          }

          const product   = result.data;
          const allImages = [product.image, ...(product.extra_images || [])].filter(Boolean);

          container.innerHTML = `
            <div class="product-image">
              <img
                id="main-product-image"
                class="main-image"
                src="shoes images/${allImages[0]}"
                alt="${product.name}"
              >
              <div class="thumbnail-gallery">
                ${allImages.map((imgSrc, index) => `
                  <img
                    class="thumbnail ${index === 0 ? 'active' : ''}"
                    src="shoes images/${imgSrc}"
                    alt="${product.name} - view ${index + 1}"
                    onclick="changeMainImage('shoes images/${imgSrc}', this)"
                  >
                `).join('')}
              </div>
            </div>

            <div class="product-info">
              <span class="brand">${product.brand}</span>
              <h2>${product.name}</h2>
              <div class="stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
              </div>
              <p class="price">UGX ${Number(product.price).toLocaleString()}</p>
              <p class="description">${product.description}</p>
              <button class="btn" id="add-to-cart-btn" data-id="${product.id}">
                <i class="fa fa-shopping-cart"></i> Add to Cart
              </button>
              <a href="${fromPage}" class="back">← Back to Shop</a>
            </div>
          `;

          // ── Add to Cart click handler ──────────────────────────────────────
          const addBtn = document.getElementById('add-to-cart-btn');

          addBtn.addEventListener('click', async () => {
            const id = addBtn.dataset.id;

            // Disable button & show spinner
            addBtn.disabled = true;
            addBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Adding...';

            try {
              const res = await fetch(`api/update_cart.php?id=${id}&action=add`);
              if (!res.ok) throw new Error('Server error ' + res.status);
              const data = await res.json();

              if (data.status === 'success' || data.qty) {
                // Success state
                addBtn.innerHTML = '<i class="fa fa-check"></i> Added to Cart!';
                addBtn.style.background = '#088178';
                showToast('Item added to cart ✓');

                // Reset button after 2 seconds
                setTimeout(() => {
                  addBtn.disabled = false;
                  addBtn.innerHTML = '<i class="fa fa-shopping-cart"></i> Add to Cart';
                  addBtn.style.background = '';
                }, 2000);
              } else {
                throw new Error('Unexpected response');
              }

            } catch (e) {
              console.error('Add to cart error:', e);
              addBtn.disabled = false;
              addBtn.innerHTML = '<i class="fa fa-shopping-cart"></i> Add to Cart';
              showToast('Could not add to cart. Try again.', true);
            }
          });

        })
        .catch(err => {
          console.error("Fetch error:", err);
          container.innerHTML = `
            <div class="error-state">
              <i class="fas fa-wifi" style="text-decoration: line-through;"></i>
              <p>Something went wrong. Please try again.</p>
              <a href="${fromPage}" class="back">← Back to Shop</a>
            </div>
          `;
        });
    }

    // ── Thumbnail switcher ────────────────────────────────────────────────────
    function changeMainImage(newSrc, clickedThumbnail) {
      document.getElementById('main-product-image').src = newSrc;
      document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
      clickedThumbnail.classList.add('active');
    }
  </script>
</body>
</html>