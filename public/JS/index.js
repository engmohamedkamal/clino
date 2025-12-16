const track = document.querySelector('.carousel-track');
const cards = document.querySelectorAll('.testimonial-card');
const next = document.querySelector('.next');
const prev = document.querySelector('.prev');

let activeIndex = 0;

function updateCarousel() {
  const gap = 30;
  const containerWidth = track.parentElement.offsetWidth;
  const cardWidth = cards[0].offsetWidth;

  // تحديث الكارد النشط
  cards.forEach((card, i) => {
    card.classList.toggle('active', i === activeIndex);
  });

  let translateX;

  if (window.innerWidth < 768) {
    // موبايل: كارد واحد فقط يبقى في النص
    translateX = activeIndex * (cardWidth + gap) - (containerWidth - cardWidth) / 2;
  } else {
    // لابتوب: الكارد النشط يكون في النص تقريباً
    translateX = activeIndex * (cardWidth + gap) - (containerWidth / 2 - cardWidth / 2);
  }

  // منع التحريك خارج الحد
  const maxTranslate = track.scrollWidth - containerWidth;
  if (translateX < 0) translateX = 0;
  if (translateX > maxTranslate) translateX = maxTranslate;

  track.style.transform = `translateX(-${translateX}px)`;
}

// أزرار next و prev
next.addEventListener('click', () => {
  activeIndex = (activeIndex + 1) % cards.length;
  updateCarousel();
});

prev.addEventListener('click', () => {
  activeIndex = (activeIndex - 1 + cards.length) % cards.length;
  updateCarousel();
});

// تحديث أول مرة
updateCarousel();

// إعادة حساب عند تغيير حجم الشاشة
window.addEventListener('resize', updateCarousel);
