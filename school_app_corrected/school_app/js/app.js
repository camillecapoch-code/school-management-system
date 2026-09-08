document.addEventListener(
    "DOMContentLoaded",
    function () {


// ==================================================
// GRADE SEARCH
// ==================================================

const gradeSearch =
    document.getElementById(
        "gradeSearch"
    );


const gradesTable =
    document.getElementById(
        "gradesTable"
    );


if (
    gradeSearch &&
    gradesTable
) {

    gradeSearch.addEventListener(
        "keyup",
        function () {

            const searchValue =
                this.value.toLowerCase();


            const rows =
                gradesTable
                .getElementsByTagName("tbody")[0]
                .getElementsByTagName("tr");


            for (
                let i = 0;
                i < rows.length;
                i++
            ) {

                const rowText =
                    rows[i]
                    .textContent
                    .toLowerCase();


                if (
                    rowText.includes(
                        searchValue
                    )
                ) {

                    rows[i].style.display =
                        "";

                } else {

                    rows[i].style.display =
                        "none";

                }

            }

        }
    );

}
        // ==================================================
        // STUDENT SEARCH
        // ==================================================

        const searchInput =
            document.getElementById(
                "studentSearch"
            );


        const studentsTable =
            document.getElementById(
                "studentsTable"
            );


        if (
            searchInput &&
            studentsTable
        ) {


            searchInput.addEventListener(
                "keyup",
                function () {


                    const searchValue =
                        this.value.toLowerCase();


                    const rows =
                        studentsTable
                        .getElementsByTagName("tbody")[0]
                        .getElementsByTagName("tr");


                    for (
                        let i = 0;
                        i < rows.length;
                        i++
                    ) {


                        const rowText =
                            rows[i]
                            .textContent
                            .toLowerCase();


                        if (
                            rowText.includes(
                                searchValue
                            )
                        ) {

                            rows[i].style.display =
                                "";

                        } else {

                            rows[i].style.display =
                                "none";

                        }

                    }

                }
            );

        }

    }
);


// ======================================================
// DELETE CONFIRMATION
// ======================================================

function confirmDelete() {

    return confirm(
        "Are you sure you want to delete this item?"
    );

}

document.addEventListener("DOMContentLoaded", function () {

    const deleteLinks =
        document.querySelectorAll(".delete-link");


    deleteLinks.forEach(function (link) {

        link.addEventListener("click", function (event) {

            const confirmed = confirm(
                "Are you sure you want to delete this grade?"
            );


            if (!confirmed) {

                event.preventDefault();

            }

        });

    });

});

document.addEventListener("DOMContentLoaded", function () {

    const menuToggle =
        document.getElementById("menuToggle");

    const navbarLinks =
        document.querySelector(".navbar-links");


    if (menuToggle && navbarLinks) {

        menuToggle.addEventListener(
            "click",
            function () {

                navbarLinks.classList.toggle(
                    "active"
                );

            }
        );

    }

});


/* =========================================
   PROFILE DROPDOWN
========================================= */

document.addEventListener("DOMContentLoaded", function () {

    const profileButton =
        document.getElementById("profileButton");

    const profileDropdown =
        document.getElementById("profileDropdown");


    if (!profileButton || !profileDropdown) {
        return;
    }


    profileButton.addEventListener(
        "click",
        function (event) {

            event.stopPropagation();

            profileDropdown.classList.toggle("show");

        }
    );


    document.addEventListener(
        "click",
        function () {

            profileDropdown.classList.remove("show");

        }
    );


    profileDropdown.addEventListener(
        "click",
        function (event) {

            event.stopPropagation();

        }
    );

});



// =========================================
// PASSWORD STRENGTH
// =========================================

const passwordInput =
    document.getElementById("new_password");

const passwordStrengthBar =
    document.getElementById("passwordStrengthBar");

const passwordStrengthText =
    document.getElementById("passwordStrengthText");


if (
    passwordInput &&
    passwordStrengthBar &&
    passwordStrengthText
) {

    passwordInput.addEventListener(
        "input",
        function () {

            const password =
                passwordInput.value;


            let score = 0;


            // Length

            if (password.length >= 8) {

                score++;

            }


            if (password.length >= 12) {

                score++;

            }


            // Uppercase

            if (/[A-Z]/.test(password)) {

                score++;

            }


            // Lowercase

            if (/[a-z]/.test(password)) {

                score++;

            }


            // Number

            if (/[0-9]/.test(password)) {

                score++;

            }


            // Special character

            if (/[\W_]/.test(password)) {

                score++;

            }


            // =====================================
            // EMPTY
            // =====================================

            if (password.length === 0) {

                passwordStrengthBar.style.width =
                    "0%";

                passwordStrengthText.textContent =
                    "Password strength";

                return;

            }


            // =====================================
            // WEAK
            // =====================================

            if (score <= 2) {

                passwordStrengthBar.style.width =
                    "25%";

                passwordStrengthText.textContent =
                    "Weak";

            }


            // =====================================
            // MEDIUM
            // =====================================

            else if (score <= 4) {

                passwordStrengthBar.style.width =
                    "50%";

                passwordStrengthText.textContent =
                    "Medium";

            }


            // =====================================
            // STRONG
            // =====================================

            else if (score === 5) {

                passwordStrengthBar.style.width =
                    "75%";

                passwordStrengthText.textContent =
                    "Strong";

            }


            // =====================================
            // VERY STRONG
            // =====================================

            else {

                passwordStrengthBar.style.width =
                    "100%";

                passwordStrengthText.textContent =
                    "Very Strong";

            }

        }
    );

}


// ==========================================
// SUN / MOON THEME TOGGLE
// ==========================================

document.addEventListener("DOMContentLoaded", function () {

    const themeToggle = document.getElementById("themeToggle");
    const sunIcon = document.getElementById("sunIcon");
    const moonIcon = document.getElementById("moonIcon");

    if (!themeToggle) return;

    function updateThemeIcon() {

        const isDark = document.body.classList.contains("dark-mode");

        if (isDark) {
            // Mode sombre → afficher la lune
            if (sunIcon) sunIcon.style.display = "none";
            if (moonIcon) moonIcon.style.display = "block";

            themeToggle.setAttribute(
                "aria-label",
                "Passer au thème clair"
            );

        } else {
            // Mode clair → afficher le soleil
            if (sunIcon) sunIcon.style.display = "block";
            if (moonIcon) moonIcon.style.display = "none";

            themeToggle.setAttribute(
                "aria-label",
                "Passer au thème sombre"
            );
        }
    }

    // Vérifier le thème sauvegardé
    const savedTheme = localStorage.getItem("schoolTheme");

    if (savedTheme === "dark") {
        document.body.classList.add("dark-mode");
    } else {
        document.body.classList.remove("dark-mode");
    }

    updateThemeIcon();

    // Clic sur le bouton
    themeToggle.addEventListener("click", function () {

        document.body.classList.toggle("dark-mode");

        const isDark =
            document.body.classList.contains("dark-mode");

        if (isDark) {
            localStorage.setItem("schoolTheme", "dark");
        } else {
            localStorage.setItem("schoolTheme", "light");
        }

        updateThemeIcon();
    });

});