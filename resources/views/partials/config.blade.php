<div class="fixed-plugin">
    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
        <i class="fa fa-cog py-2"> </i>
    </a>
    <div class="card shadow-lg blur">
        <div class="card-header pb-0 pt-3  bg-transparent ">
            <div class="float-start">
                <h5 class="mt-3 mb-0">Soft UI Configurator</h5>
                <p>See our dashboard options.</p>
            </div>
            <div class="float-end mt-4">
                <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
                    <i class="fa fa-close"></i>
                </button>
            </div>
            <!-- End Toggle Button -->
        </div>
        <hr class="horizontal dark my-1">
        <div class="card-body pt-sm-3 pt-0">
            <!-- Sidebar Backgrounds -->
            <div>
                <h6 class="mb-0">Sidebar Colors</h6>
            </div>
            <a class="switch-trigger background-color" href="javascript:void(0)">
                <div class="badge-colors my-2 text-start">
                                    <span class="badge filter bg-gradient-primary active" data-color="primary"
                                          onclick="sidebarColor(this)"></span>
                    <span class="badge filter bg-gradient-dark" data-color="dark" onclick="sidebarColor(this)"></span>
                    <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
                    <span class="badge filter bg-gradient-success" data-color="success"
                          onclick="sidebarColor(this)"></span>
                    <span class="badge filter bg-gradient-warning" data-color="warning"
                          onclick="sidebarColor(this)"></span>
                    <span class="badge filter bg-gradient-danger" data-color="danger"
                          onclick="sidebarColor(this)"></span>
                </div>
            </a>
            <!-- Sidenav Type -->
            <div class="mt-3">
                <h6 class="mb-0">Sidenav Type</h6>
                <p class="text-sm">Choose between 2 different sidenav types.</p>
            </div>
            <div class="d-flex">
                <button class="btn bg-gradient-primary w-100 px-3 mb-2 active" data-class="bg-transparent"
                        onclick="sidebarType(this)">Transparent
                </button>
                <button class="btn bg-gradient-primary w-100 px-3 mb-2 ms-2" data-class="bg-white"
                        onclick="sidebarType(this)">White
                </button>
            </div>
            <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
            <!-- Navbar Fixed -->
            <div class="mt-3">
                <h6 class="mb-0">Navbar Fixed</h6>
            </div>
            <div class="form-check form-switch ps-0">
                <input class="form-check-input mt-1 ms-auto" id="navbarFixed" onclick="navbarFixed(this)"
                       type="checkbox">
            </div>
            <hr class="horizontal dark mb-1 d-xl-block d-none">
            <div class="mt-2 d-xl-block d-none">
                <h6 class="mb-0">Sidenav Mini</h6>
            </div>
            <div class="form-check form-switch ps-0 d-xl-block d-none">
                <input class="form-check-input mt-1 ms-auto" id="navbarMinimize" onclick="navbarMinimize(this)"
                       type="checkbox">
            </div>
            <hr class="horizontal dark mb-1 d-xl-block d-none">
            <div class="mt-2 d-xl-block d-none">
                <h6 class="mb-0">Light/Dark</h6>
            </div>
            <div class="form-check form-switch ps-0 d-xl-block d-none">
                <input class="form-check-input mt-1 ms-auto" id="dark-version" onclick="darkMode(this)" type="checkbox">
            </div>
            <hr class="horizontal dark my-sm-4">
            <a class="btn bg-gradient-info w-100" href="https://www.creative-tim.com/product/soft-ui-dashboard-pro">Buy
                now</a>
            <a class="btn bg-gradient-dark w-100" href="https://www.creative-tim.com/product/soft-ui-dashboard">Free
                demo</a>
            <a class="btn btn-outline-dark w-100"
               href="https://www.creative-tim.com/learning-lab/bootstrap/overview/soft-ui-dashboard">View
                documentation</a>
            <div class="w-100 text-center">
                <a aria-label="Star creativetimofficial/soft-ui-dashboard on GitHub" class="github-button"
                   data-icon="octicon-star" data-show-count="true" data-size="large"
                   href="https://github.com/creativetimofficial/ct-soft-ui-dashboard-pro">Star</a>
                <h6 class="mt-3">Thank you for sharing!</h6>
                <a class="btn btn-dark mb-0 me-2"
                   href="https://twitter.com/intent/tweet?text=Check%20Soft%20UI%20Dashboard%20PRO%20made%20by%20%40CreativeTim%20%23webdesign%20%23dashboard%20%23bootstrap5&amp;url=https%3A%2F%2Fwww.creative-tim.com%2Fproduct%2Fsoft-ui-dashboard-pro" target="_blank">
                    <i aria-hidden="true" class="fab fa-twitter me-1"></i> Tweet
                </a>
                <a class="btn btn-dark mb-0 me-2"
                   href="https://www.facebook.com/sharer/sharer.php?u=https://www.creative-tim.com/product/soft-ui-dashboard-pro" target="_blank">
                    <i aria-hidden="true" class="fab fa-facebook-square me-1"></i> Share
                </a>
            </div>
        </div>
    </div>
</div>
