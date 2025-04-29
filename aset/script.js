document.addEventListener("DOMContentLoaded", function () {
  const observer = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("show");
          observer.unobserve(entry.target); // Hentikan pengamatan setelah elemen muncul
        }
      });
    },
    { threshold: 0.1 } // Elemen akan muncul saat 10% terlihat
  );

  // Pilih semua elemen dengan kelas "hidden"
  const hiddenElements = document.querySelectorAll(".hidden");
  hiddenElements.forEach((el) => observer.observe(el));
});

// // Fungsi untuk menghapus item dari keranjang dengan konfirmasi
// function removeFromCart(id) {
//   // Cari item yang akan dihapus untuk mendapatkan namanya
//   let cart = JSON.parse(localStorage.getItem("cart")) || [];
//   let itemToRemove = cart.find((item) => item.id === id);

//   if (!itemToRemove) return;

//   // Tampilkan dialog konfirmasi
//   if (
//     confirm(
//       `Apakah Anda yakin ingin menghapus ${itemToRemove.name} dari keranjang?`
//     )
//   ) {
//     cart = cart.filter((item) => item.id !== id);
//     localStorage.setItem("cart", JSON.stringify(cart));
//     loadCart();
//     updateCartCount();

//     // Tampilkan notifikasi bahwa item telah dihapus
//     alert(`${itemToRemove.name} telah dihapus dari keranjang.`);
//   }
// }

// // Fungsi untuk checkout dengan konfirmasi
// function checkout() {
//   let cart = JSON.parse(localStorage.getItem("cart")) || [];
//   if (cart.length === 0) return;

//   // Hitung total belanja
//   let total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
//   let totalWithTax = total * 1.11; // Tambah PPN 11%

//   if (
//     confirm(
//       `Anda akan melakukan checkout dengan total pembayaran Rp ${totalWithTax.toLocaleString(
//         "id-ID"
//       )}.\nLanjutkan?`
//     )
//   ) {
//     alert(
//       "Pesanan Anda telah diterima! Terima kasih telah berbelanja di Dapur Aizlan."
//     );
//     localStorage.removeItem("cart");
//     loadCart();
//     updateCartCount();
//   }
// }

// // Event listener untuk tombol checkout
// document.getElementById("checkout-btn").addEventListener("click", function () {
//   checkout().catch((error) => console.error("Error during checkout:", error));
// });
