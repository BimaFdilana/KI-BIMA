 document.addEventListener('DOMContentLoaded', function() {
         const otpInputs = document.querySelectorAll("input[name^='digit']");

        otpInputs.forEach((input, index) => {
            input.addEventListener('keydown', function(event) {
                if (event.key === 'Backspace' && input.value === '') {
                    if (index !== 0) {
                        otpInputs[index - 1].focus();
                    }
                } else if (event.key === 'ArrowLeft' && index !== 0) {
                    otpInputs[index - 1].focus();
                } else if (event.key === 'ArrowRight' && index !== otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('input', function(event) {
                if (input.value.length === 1 && index !== otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('paste', function(event) {
                event.preventDefault();
                const pasteData = (event.clipboardData || window.clipboardData).getData('text').trim();
                const pasteArray = pasteData.split('');
                pasteArray.forEach((char, pasteIndex) => {
                    if (pasteIndex < otpInputs.length) {
                        otpInputs[pasteIndex].value = char;
                    }
                });
                otpInputs[Math.min(pasteArray.length, otpInputs.length) - 1].focus();
            });
        });
    });
    
//   document.addEventListener('DOMContentLoaded', function() {
//         const form = document.querySelector('form');
//         const input = document.getElementById('identifier');

//         input.addEventListener('keydown', function(event) {
//             if (event.key === 'Enter') {
//                 event.preventDefault(); // Mencegah aksi default tombol Enter
//                 form.submit(); // Memicu submit form
//             }
//         });
//     });


function updateResendCountdown() {
    var countdownElement = document.getElementById('resend-countdown');
    var resendLink = document.getElementById('resend-link');

    if (countdownElement && resendLink) {
        // Ambil waktu terakhir OTP dikirimkan dari data HTML
        var lastOTPSentTime = new Date(countdownElement.getAttribute('data-last-otp-sent-time'));
        var currentTime = new Date();

        // Hitung waktu yang telah berlalu sejak waktu terakhir OTP dikirimkan
        var timeDifference = (currentTime.getTime() - lastOTPSentTime.getTime()) / 1000; // Dalam detik
        var timeoutSeconds = 90;
        var countdownSeconds = timeoutSeconds - Math.floor(timeDifference);

        if (countdownSeconds > 0) {
            var minutes = Math.floor(countdownSeconds / 60);
            var seconds = countdownSeconds % 60;
            countdownElement.textContent = minutes + 'm ' + seconds + 's';
            resendLink.classList.add('hidden'); // Sembunyikan tautan Resend
        } else {
            // Waktu habis, aktifkan tautan Resend
            resendLink.classList.remove('hidden');
            countdownElement.textContent = ''; // Kosongkan teks hitungan mundur
        }
    }
}
updateResendCountdown();
setInterval(updateResendCountdown, 1000);




