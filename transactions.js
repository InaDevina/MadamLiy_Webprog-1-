const cart = {};

const cartContainer = document.getElementById("cart-items");
const subtotalEl = document.getElementById("subtotal");
const taxEl = document.getElementById("tax");
const totalEl = document.getElementById("total");

document.addEventListener("DOMContentLoaded", () => {

  document.querySelectorAll(".add-btn").forEach(button => {

    button.addEventListener("click", () => {

      const card = button.closest(".card");

      const name =
        card.querySelector(".product-name").textContent;

      const price =
        parseInt(
          card.querySelector(".price")
            .dataset.price
        );

      if (cart[name]) {

        cart[name].qty++;

      } else {

        cart[name] = {
          name,
          price,
          qty: 1
        };
      }

      renderCart();

    });

  });

});

function renderCart() {

  const emptyCart =
        document.getElementById("empty-cart");

    const checkoutSection =
        document.getElementById("checkout-section");

    if(Object.keys(cart).length === 0){

        emptyCart.style.display = "block";
        checkoutSection.style.display = "none";

    }else{

        emptyCart.style.display = "none";
        checkoutSection.style.display = "block";

    }

  cartContainer.innerHTML = "";

  let subtotal = 0;

  Object.values(cart).forEach(item => {

    subtotal += item.price * item.qty;

    cartContainer.innerHTML += `
            <div class="order-item">

                <div>

                    <strong>${item.name}</strong>

                    <br>

                    <span>
                        Rp ${item.price.toLocaleString('id-ID')}
                    </span>

                </div>

                <div class="qty">

                    <button
                        class="minus-btn"
                        data-name="${item.name}">
                        -
                    </button>

                    <strong>
                        ${item.qty}
                    </strong>

                    <button
                        class="plus-btn"
                        data-name="${item.name}">
                        +
                    </button>

                </div>

            </div>
        `;

  });

  const tax = subtotal * 0.1;
  const total = subtotal + tax;

  subtotalEl.textContent =
    "Rp " + subtotal.toLocaleString("id-ID");

  taxEl.textContent =
    "Rp " + tax.toLocaleString("id-ID");

  totalEl.textContent =
    "Rp " + total.toLocaleString("id-ID");

}

document.addEventListener("click", (e) => {

  if (e.target.classList.contains("plus-btn")) {

    const name =
      e.target.dataset.name;

    cart[name].qty++;

    renderCart();
  }

  if (e.target.classList.contains("minus-btn")) {

    const name =
      e.target.dataset.name;

    cart[name].qty--;

    if (cart[name].qty <= 0) {
      delete cart[name];
    }

    renderCart();
  }

});

document.querySelector(".clear")
  ?.addEventListener("click", () => {

    Object.keys(cart).forEach(key => {
      delete cart[key];
    });

    renderCart();

  });

  document.addEventListener("DOMContentLoaded", () => {

    const logoutBtn = document.querySelector(".logout");

    logoutBtn.addEventListener("click", () => {

        const confirmLogout = confirm(
            "Apakah Anda yakin ingin logout?"
        );

        if(confirmLogout){

            window.location.href = "login.html";

        }

    });

});
