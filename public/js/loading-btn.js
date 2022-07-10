function loading() {
  let submit = document.getElementById("form-submit");
  let loading = document.getElementById("loading");
  if (submit) {
    submit.style.pointerEvents = "none";
    if (submit && loading) {
      submit.style.display = "none";
      loading.style.display = "block";
    }
  }
}
