<footer class="app-footer bg-body-secondary py-3 border-top" dir="rtl">
    <div class="container">
        <div class="row align-items-center">
            <!-- حقوق النشر -->
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <span class="d-inline-block">
                    &copy; <span id="year"></span> جميع الحقوق محفوظة لـ
                    <a  class="text-primary text-decoration-none fw-bold">هلال الوزان سوفت</a>
                </span>
            </div>
            
            <!-- وسائل التواصل -->
            <div class="col-md-6">
                <div class="d-flex justify-content-center justify-content-md-end gap-3">
                    <a href="tel:+967 773 355 465" class="text-decoration-none" title="اتصل بنا">
                        {{-- <span class="d-none d-md-inline">773355465</span> --}}
                        <i class="fas fa-phone-alt ms-1 text-primary"></i>
                    </a>
                    
                    <a href="mailto:helalalwazzan1@gmail.com" class="text-decoration-none" title="البريد الإلكتروني">
                        {{-- <span class="d-none d-md-inline">helalalwazzan1@gmail.com</span> --}}
                        <i class="fas fa-envelope ms-1 text-danger"></i>
                    </a>
                    
                    <a href="https://wa.me/+967773355465" target="_blank" class="text-decoration-none" title="واتساب">
                        {{-- <span class="d-none d-md-inline">واتساب</span> --}}
                        <i class="fab fa-whatsapp ms-1 text-success"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</footer>

<style>
    .app-footer a {
        transition: all 0.3s ease;
        padding: 5px 8px;
        border-radius: 4px;
    }
    
    .app-footer a:hover {
        background-color: rgba(0,0,0,0.05);
        transform: translateY(-2px);
    }
    
    /* @media (max-width: 767.98px) {
        .app-footer .col-md-6 {
            text-align: center !important;
        }
    } */
</style>