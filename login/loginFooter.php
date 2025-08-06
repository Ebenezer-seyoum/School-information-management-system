<div class="row pt-5 mt-5 text-center">
          <div class="col-md-12" class="footer-text">
            <p>
            Copyright &copy;<script>document.write(new Date().getFullYear());</script> 
              Balela Secondary School - All rights reserved |
            </p>
          </div>          
        </div>
      </div>
        <!-- JavaScript Libraries -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="login/assets/lib/wow/wow.min.js"></script>
        <script src="login/assets/lib/easing/easing.min.js"></script>
        <script src="login/assets/lib/waypoints/waypoints.min.js"></script>
        <script src="login/assets/lib/owlcarousel/owl.carousel.min.js"></script>    
        <!-- Template Javascript -->
        <script src="js/main.js"></script>
        <!-- Toggle Password Script -->
<script>
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function () {
            const target = document.getElementById(this.getAttribute('data-target'));
            if (target.type === "password") {
                target.type = "text";
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                target.type = "password";
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });
</script>
</html>

