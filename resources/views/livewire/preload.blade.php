  <style>
      .socket {
          width: 200px;
          height: 200px;
          position: absolute;
          left: 50%;
          margin-left: -100px;
          top: 50%;
          margin-top: -100px;
      }

      .hex-brick {
          background: #ABF8FF;
          width: 30px;
          height: 17px;
          position: absolute;
          top: 5px;
          animation-name: fade;
          animation-duration: 2s;
          animation-iteration-count: infinite;
          -webkit-animation-name: fade;
          -webkit-animation-duration: 2s;
          -webkit-animation-iteration-count: infinite;
      }

      .h2 {
          transform: rotate(60deg);
          -webkit-transform: rotate(60deg);
      }

      .h3 {
          transform: rotate(-60deg);
          -webkit-transform: rotate(-60deg);
      }

      .gel {
          height: 30px;
          width: 30px;
          transition: all .3s;
          -webkit-transition: all .3s;
          position: absolute;
          top: 50%;
          left: 50%;
      }

      .center-gel {
          margin-left: -15px;
          margin-top: -15px;
          animation-name: pulses;
          animation-duration: 2s;
          animation-iteration-count: infinite;
          -webkit-animation-name: pulses;
          -webkit-animation-duration: 2s;
          -webkit-animation-iteration-count: infinite;
      }

      .c1 {
          margin-left: -47px;
          margin-top: -15px;
      }

      .c2 {
          margin-left: -31px;
          margin-top: -43px;
      }

      .c3 {
          margin-left: 1px;
          margin-top: -43px;
      }

      .c4 {
          margin-left: 17px;
          margin-top: -15px;
      }

      .c5 {
          margin-left: -31px;
          margin-top: 13px;
      }

      .c6 {
          margin-left: 1px;
          margin-top: 13px;
      }

      .c7 {
          margin-left: -63px;
          margin-top: -43px;
      }

      .c8 {
          margin-left: 33px;
          margin-top: -43px;
      }

      .c9 {
          margin-left: -15px;
          margin-top: 41px;
      }

      .c10 {
          margin-left: -63px;
          margin-top: 13px;
      }

      .c11 {
          margin-left: 33px;
          margin-top: 13px;
      }

      .c12 {
          margin-left: -15px;
          margin-top: -71px;
      }

      .c13 {
          margin-left: -47px;
          margin-top: -71px;
      }

      .c14 {
          margin-left: 17px;
          margin-top: -71px;
      }

      .c15 {
          margin-left: -47px;
          margin-top: 41px;
      }

      .c16 {
          margin-left: 17px;
          margin-top: 41px;
      }

      .c17 {
          margin-left: -79px;
          margin-top: -15px;
      }

      .c18 {
          margin-left: 49px;
          margin-top: -15px;
      }

      .c19 {
          margin-left: -63px;
          margin-top: -99px;
      }

      .c20 {
          margin-left: 33px;
          margin-top: -99px;
      }

      .c21 {
          margin-left: 1px;
          margin-top: -99px;
      }

      .c22 {
          margin-left: -31px;
          margin-top: -99px;
      }

      .c23 {
          margin-left: -63px;
          margin-top: 69px;
      }

      .c24 {
          margin-left: 33px;
          margin-top: 69px;
      }

      .c25 {
          margin-left: 1px;
          margin-top: 69px;
      }

      .c26 {
          margin-left: -31px;
          margin-top: 69px;
      }

      .c27 {
          margin-left: -79px;
          margin-top: -15px;
      }

      .c28 {
          margin-left: -95px;
          margin-top: -43px;
      }

      .c29 {
          margin-left: -95px;
          margin-top: 13px;
      }

      .c30 {
          margin-left: 49px;
          margin-top: 41px;
      }

      .c31 {
          margin-left: -79px;
          margin-top: -71px;
      }

      .c32 {
          margin-left: -111px;
          margin-top: -15px;
      }

      .c33 {
          margin-left: 65px;
          margin-top: -43px;
      }

      .c34 {
          margin-left: 65px;
          margin-top: 13px;
      }

      .c35 {
          margin-left: -79px;
          margin-top: 41px;
      }

      .c36 {
          margin-left: 49px;
          margin-top: -71px;
      }

      .c37 {
          margin-left: 81px;
          margin-top: -15px;
      }

      .r1 {
          animation-name: pulses;
          animation-duration: 2s;
          animation-iteration-count: infinite;
          animation-delay: .2s;
          -webkit-animation-name: pulses;
          -webkit-animation-duration: 2s;
          -webkit-animation-iteration-count: infinite;
          -webkit-animation-delay: .2s;
      }

      .r2 {
          animation-name: pulses;
          animation-duration: 2s;
          animation-iteration-count: infinite;
          animation-delay: .4s;
          -webkit-animation-name: pulses;
          -webkit-animation-duration: 2s;
          -webkit-animation-iteration-count: infinite;
          -webkit-animation-delay: .4s;
      }

      .r3 {
          animation-name: pulses;
          animation-duration: 2s;
          animation-iteration-count: infinite;
          animation-delay: .6s;
          -webkit-animation-name: pulses;
          -webkit-animation-duration: 2s;
          -webkit-animation-iteration-count: infinite;
          -webkit-animation-delay: .6s;
      }

      .r1>.hex-brick {
          animation-name: fade;
          animation-duration: 2s;
          animation-iteration-count: infinite;
          animation-delay: .2s;
          -webkit-animation-name: fade;
          -webkit-animation-duration: 2s;
          -webkit-animation-iteration-count: infinite;
          -webkit-animation-delay: .2s;
      }

      .r2>.hex-brick {
          animation-name: fade;
          animation-duration: 2s;
          animation-iteration-count: infinite;
          animation-delay: .4s;
          -webkit-animation-name: fade;
          -webkit-animation-duration: 2s;
          -webkit-animation-iteration-count: infinite;
          -webkit-animation-delay: .4s;
      }

      .r3>.hex-brick {
          animation-name: fade;
          animation-duration: 2s;
          animation-iteration-count: infinite;
          animation-delay: .6s;
          -webkit-animation-name: fade;
          -webkit-animation-duration: 2s;
          -webkit-animation-iteration-count: infinite;
          -webkit-animation-delay: .6s;
      }

      @keyframes pulses {
          0% {
              -webkit-transform: scale(1);
              transform: scale(1);
          }

          50% {
              -webkit-transform: scale(0.01);
              transform: scale(0.01);
          }

          100% {
              -webkit-transform: scale(1);
              transform: scale(1);
          }
      }

      @keyframes fade {
          0% {
              background: #ABF8FF;
          }

          50% {
              background: #90BBBF;
          }

          100% {
              background: #ABF8FF;
          }
      }

      @-webkit-keyframes pulsess {
          0% {
              -webkit-transform: scale(1);
              transform: scale(1);
          }

          50% {
              -webkit-transform: scale(0.01);
              transform: scale(0.01);
          }

          100% {
              -webkit-transform: scale(1);
              transform: scale(1);
          }
      }

      @-webkit-keyframes fade {
          0% {
              background: #ABF8FF;
          }

          50% {
              background: #389CA6;
          }

          100% {
              background: #ABF8FF;
          }
      }
  </style>
  <div class="socket">
      <div class="gel center-gel">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c1 r1">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c2 r1">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c3 r1">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c4 r1">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c5 r1">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c6 r1">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>

      <div class="gel c7 r2">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>

      <div class="gel c8 r2">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c9 r2">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c10 r2">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c11 r2">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c12 r2">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c13 r2">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c14 r2">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c15 r2">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c16 r2">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c17 r2">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c18 r2">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c19 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c20 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c21 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c22 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c23 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c24 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c25 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c26 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c28 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c29 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c30 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c31 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c32 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c33 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c34 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c35 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c36 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="gel c37 r3">
          <div class="hex-brick h1"></div>
          <div class="hex-brick h2"></div>
          <div class="hex-brick h3"></div>
      </div>
      <div class="flex h-[68vh] w-full items-center justify-center text-cyan-200">
          <div>
              <h1 class="flex items-center text-lg font-bold">L
                  <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                      class="animate-spin" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                      <path
                          d="M12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2ZM13.6695 15.9999H10.3295L8.95053 17.8969L9.5044 19.6031C10.2897 19.8607 11.1286 20 12 20C12.8714 20 13.7103 19.8607 14.4956 19.6031L15.0485 17.8969L13.6695 15.9999ZM5.29354 10.8719L4.00222 11.8095L4 12C4 13.7297 4.54894 15.3312 5.4821 16.6397L7.39254 16.6399L8.71453 14.8199L7.68654 11.6499L5.29354 10.8719ZM18.7055 10.8719L16.3125 11.6499L15.2845 14.8199L16.6065 16.6399L18.5179 16.6397C19.4511 15.3312 20 13.7297 20 12L19.997 11.81L18.7055 10.8719ZM12 9.536L9.656 11.238L10.552 14H13.447L14.343 11.238L12 9.536ZM14.2914 4.33299L12.9995 5.27293V7.78993L15.6935 9.74693L17.9325 9.01993L18.4867 7.3168C17.467 5.90685 15.9988 4.84254 14.2914 4.33299ZM9.70757 4.33329C8.00021 4.84307 6.53216 5.90762 5.51261 7.31778L6.06653 9.01993L8.30554 9.74693L10.9995 7.78993V5.27293L9.70757 4.33329Z">
                      </path>
                  </svg> ading . . .
              </h1>
          </div>
      </div>
      </button>
      <button onclick="closedLoad()" id="closed" type="button"
          class="disabled fixed right-0 top-5 me-2 inline-flex items-center rounded-lg border border-gray-700 bg-gray-800 px-2.5 py-1.5 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
          <svg aria-hidden="true" role="status" class="me-3 inline h-4 w-4 animate-spin text-gray-600"
              viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                  d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                  fill="currentColor" />
              <path
                  d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                  fill="#1C64F2" />
          </svg>
          Loading...
      </button>
  </div>
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          // Menampilkan preloader saat halaman dimuat
          document.getElementById('preloader').classList.remove('hidden');
          document.getElementById('bodyS').classList.add('overflow-hidden');
          // Menghilangkan preloader setelah konten dimuat
          window.addEventListener('load', function() {
              document.getElementById('preloader').classList.add('hidden');
              document.getElementById('bodyS').classList.remove('overflow-hidden');
          });

          function loadData() {
              // Tampilkan preloader
              document.getElementById('preloader').classList.remove('hidden');
              document.getElementById('bodyS').classList.add('overflow-hidden');
              // Lakukan pengambilan data atau operasi lainnya
              setTimeout(function() {
                  document.getElementById('preloader').classList.add('hidden');
                  document.getElementById('bodyS').classList.remove('overflow-hidden');
              }, 2000);
          }

          // Panggil fungsi loadData() di mana pun Anda membutu
      });
  </script>
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          setTimeout(function() {
              const button = document.getElementById('closed');
              button.classList.remove('disabled');
              button.classList.remove('bg-gray-800');
              button.classList.remove('text-gray-700');
              button.classList.add('bg-gray-900');
              button.classList.add('text-gray-200')
              button.innerHTML = `
                <svg aria-hidden="true" role="status" class="me-3 inline h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Preload
            `;
          }, 3000);
      });

      function closedLoad() {
          const button = document.getElementById('closed');
          if (!button.classList.contains('disabled')) {
              document.getElementById('preloader').classList.add('hidden');
              document.getElementById('bodyS').classList.remove('overflow-hidden');
          }
      }
  </script>
