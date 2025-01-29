function show_search_block() {
  let search_form = document.querySelector(".search-div-flex");
  if (search_form.style.display == "block") search_form.style.display = "none";
  else search_form.style.display = "block";
}

function show_login_block() {
  let login_form = document.querySelector(".login-form");
  login_form.classList.toggle("hidden");
}

function darkmode(btn) {
  let body = document.body;
  let img = document.querySelector(".welcome_img");
  if (body.classList.contains("dark")) {
    // إذا كان الكلاس موجودًا، قم بإزالته
    body.classList.remove("dark");
    btn.textContent = "🌙";
    img.src = "images/index_img.png";
  } else {
    // إذا لم يكن الكلاس موجودًا، قم بإضافته
    body.classList.add("dark");
    btn.textContent = "☀️";
    img.src = "images/index_dark_img.png";
  }
}


// SOCIAL PANEL JS
// let floating_btn = document.querySelector(".floating-btn");
// let social_panel_container = document.querySelector(".side-bar");

// floating_btn.addEventListener("click", () => {
//   if (social_panel_container.classList.contains("visible")) {
//     social_panel_container.classList.remove("visible");
//   } else {
//     social_panel_container.classList.add("visible");
//   }
// });

function show_side_bar() {
  let social_panel_container = document.querySelector(".side-bar");
  if (social_panel_container.classList.contains("visible")) {
    social_panel_container.classList.remove("visible");
  } else {
    social_panel_container.classList.add("visible");
  }
}



//دالة اختيار النشاط عند المدرس
function toggle_homewarke(status) {
  let normal = document.getElementById("normal_homewarke");
  let options = document.getElementById("options_homewarke");
  if (status.value == "عادي") {
    normal.style.display = "block";
    options.style.display = "none";
  } else if (status.value == "مؤتمت") {
    normal.style.display = "none";
    options.style.display = "block";
  }
}


function show_div(statu, section_btn = '') {

  // حفظ الحالة في LocalStorage
  localStorage.setItem("statuDiv", statu);
  //حفظ الزر الذي تم الضغط عليه
  localStorage.setItem('selectedsection', section_btn.innerText);


  // إخفاء جميع الأقسام
  document.querySelectorAll(".informations").forEach((div) => {
    div.classList.remove("active");;

  });

  //الغاء تمييز جميع الازرار
  document.querySelectorAll(".section-btn").forEach((btn) => {
    btn.classList.remove("active");
  });

  // إظهار القسم المطلوب
  document.getElementById(statu).classList.add("active");
  //تمييز الزر المضغوط
  section_btn.classList.add("active");

}



function show_usecase(usecase, usecase_btn, default_section) {
  let social_panel_container = document.querySelector(".side-bar");
  // حفظ الحالة في LocalStorage
  localStorage.setItem("usecaseDiv", usecase);
  //حفظ الزر الذي تم الضغط عليه
  localStorage.setItem('selected_usecase', usecase_btn.innerText);

  // إخفاء جميع الأقسام
  document.querySelectorAll(".inside-div").forEach((div) => {
    div.classList.remove("active");
  });

  //الغاء تمييز جميع الازرار
  document.querySelectorAll(".usecase-btn").forEach((btn) => {
    btn.classList.remove("active");
  });


  // اخفاء السايد بار في حالة تصغير الشاشة
  social_panel_container.classList.remove("visible");


  // إظهار القسم المطلوب
  document.getElementById(usecase).classList.add("active");
  //تمييز الزر المضغوط
  usecase_btn.classList.add("active");
  //لضغط زر اول خيار في التبوبات للسيكشن المختار
  document.getElementById(default_section).click();

}

/************تصغير العداد********** */
function min_div() {
  $time_div = document.getElementById("time_div");
  $max_div = document.getElementsByClassName("max")[0];
  $time_div.classList.toggle("min-div");
  $max_div.classList.toggle("max_hidden");
}
/******************اظهر ديف الوقت و اخفائه*************** */
function toggel_hidden() {
  let $hidden_div = document.getElementById("timeDiv");
  $hidden_div.classList.toggle("hidden");
}

// ###########################################################
//يجب ان تكون آخر سطر في الجافاسكريبت حصرا
// استعادة الحالة عند تحميل الصفحة
window.onload = function () {
  //يتم استرجاع آخر حالة تم الضغط عليها لاعادة اظهارها بعد تحميل الصفحة
  const usecaseDiv = localStorage.getItem("usecaseDiv");
  const usecaseSlected = localStorage.getItem("selected_usecase");
  const sectionDiv = localStorage.getItem("statuDiv");
  const sectionSlected = localStorage.getItem("selectedsection");


  //اظهار الديف الاخير بعد التحديث
  if (usecaseDiv && usecaseSlected) {
    use = document.getElementById(usecaseDiv);
    use.classList.add("active");
  }

  // استعادة تمييز الزر اليوزكيس النشط
  var buttons = document.querySelectorAll('.usecase-btn');
  buttons.forEach(function (btn) {
    if (btn.innerText === usecaseSlected) {
      btn.classList.add('active');
    }
  });

  //اظهار السيكتشن الاخير بعد التحديث
  if (sectionDiv && sectionSlected) {
    use = document.getElementById(sectionDiv);
    use.classList.add("active");
  }

  // استعادة تمييز الزر سيكتشن النشط
  var buttons = document.querySelectorAll('.section-btn');
  buttons.forEach(function (btn) {
    if (btn.innerText === sectionSlected) {
      btn.classList.add('active');
    }
  });
}

// ###########################################################/
