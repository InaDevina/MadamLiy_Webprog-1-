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

  const cartItems =
    document.getElementById("cart-items");

  const emptyCart =
    document.getElementById("empty-cart");

  const checkoutSection =
    document.getElementById("checkout-section");

  const cartCard =
    document.getElementById("cart-card");

  const itemCount =
    Object.keys(cart).length;

  if (itemCount === 0) {

    emptyCart.classList.remove("hidden");

    cartItems.classList.add("hidden");

    checkoutSection.classList.add("hidden");

    cartCard.classList.remove("h-[650px]");
    cartCard.classList.add("h-[220px]");

    cartCard.classList.remove("rounded-b-none");
    cartCard.classList.add("rounded-[16px]");

  } else {

    emptyCart.classList.add("hidden");

    cartItems.classList.remove("hidden");

    checkoutSection.classList.remove("hidden");

    cartCard.classList.remove("h-[220px]");
    cartCard.classList.add("h-[220px]");

    cartCard.classList.add("rounded-b-none");
    checkoutSection.classList.add("rounded-t-none");

  }
  cartContainer.innerHTML = "";

  let subtotal = 0;

  Object.values(cart).forEach(item => {

    subtotal += item.price * item.qty;

    cartContainer.innerHTML += `
            <div class="order-item flex items-center justify-between gap-4 border-b border-[#e5e7eb] py-4 last:border-b-0">

                <div>
                    <strong class="block text-sm text-[#111827]">${item.name}</strong>
                    <span class="text-[#6b7280] text-sm">Rp ${item.price.toLocaleString('id-ID')}</span>
                </div>

                <div class="qty inline-flex items-center rounded-[12px] border border-[#e5e7eb] overflow-hidden bg-[#f8fafb]">
                    <button
                        class="minus-btn px-3 h-9 text-[#111827] bg-white hover:bg-[#f3f4f6] transition"
                        data-name="${item.name}">
                        -
                    </button>

                    <strong class="px-4 text-sm text-[#111827]">
                        ${item.qty}
                    </strong>

                    <button
                        class="plus-btn px-3 h-9 text-[#111827] bg-white hover:bg-[#f3f4f6] transition"
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

    if (confirmLogout) {

      window.location.href = "login.html";

    }

  });

});
