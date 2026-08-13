const urlParams = new URLSearchParams(window.location.search);
const status = urlParams.get("status");
const action = urlParams.get("action");

if (status === "success") {
  if (action === "registered") {
    Swal.fire({
      icon: "success",
      title: "Berhasil!",
      text: "Akun berhasil terdaftar. Silakan login 😁",
      timer: 3000,
      showConfirmButton: false,
    });
  } else if (action === "deleteuser") {
    Swal.fire({
      icon: "success",
      title: "Berhasil!",
      text: "Akun anda telah berhasil dihapus 😁",
      timer: 3000,
      showConfirmButton: false,
    });
  }
} else if (status === "error") {
  if (action === "login") {
    Swal.fire({
      icon: "error",
      title: "Gagal!",
      text: "Username atau password salah 🤬",
      timer: 3000,
      showConfirmButton: false,
    });
  }
}
