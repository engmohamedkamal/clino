(function () {
  const section = document.querySelector("#Testimonials");
  if (!section) return;

  const area = section.querySelector(".carousel-area");
  const track = section.querySelector(".carousel-track");
  const prevBtn = section.querySelector(".arrow.prev");
  const nextBtn = section.querySelector(".arrow.next");

  if (!area || !track || !prevBtn || !nextBtn) return;

  // ✅ خلي الكارت اللي عايزه يبقى Active أول ما الصفحة تفتح
  // Card 1 => 0, Card 2 => 1, Card 3 => 2 ...
  let index = 0; // غيرها لـ 2 لو عايز Card 3 تبدأ Active

  const clamp = (v, min, max) => Math.max(min, Math.min(max, v));

  function update() {
    const cards = Array.from(track.querySelectorAll(".testimonial-card"));
    if (!cards.length) return;

    index = clamp(index, 0, cards.length - 1);

    // ✅ Active class
    cards.forEach((c, i) => c.classList.toggle("active", i === index));

    // ===== حساب الحركة =====
    const areaStyles = getComputedStyle(area);
    const padL = parseFloat(areaStyles.paddingLeft) || 0;
    const padR = parseFloat(areaStyles.paddingRight) || 0;

    // المساحة المرئية الحقيقية (بعد الـ padding)
    const visible = area.clientWidth - padL - padR;

    const cardW = cards[index].getBoundingClientRect().width;

    let x;

    if (window.innerWidth < 768) {
      // ✅ Mobile: كارت 100% (محاذاة بداية)
      x = cards[index].offsetLeft;
    } else {
      // ✅ Desktop/Tablet: سنترة الكارت النشط
      x = cards[index].offsetLeft - padL - (visible / 2 - cardW / 2);
    }

    // ✅ منع الخروج بره حدود التراك
    const maxX = Math.max(0, track.scrollWidth - visible);
    x = clamp(x, 0, maxX);

    track.style.transform = `translateX(-${x}px)`;

    // ✅ buttons enable/disable
    prevBtn.disabled = index === 0;
    nextBtn.disabled = index === cards.length - 1;
  }

  nextBtn.addEventListener("click", () => {
    const cards = track.querySelectorAll(".testimonial-card");
    if (index < cards.length - 1) index++;
    update();
  });

  prevBtn.addEventListener("click", () => {
    if (index > 0) index--;
    update();
  });



  window.addEventListener("resize", () => {
    requestAnimationFrame(update);
  });

  // init
  update();
})();
