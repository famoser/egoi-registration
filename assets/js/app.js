import '../css/app.scss'
import './fontawesome'

const $ = require('jquery')
require('bootstrap')
require('typeface-open-sans')
require('bootstrap-select')
require('flatpickr')
require('jquery-autocompleter')

// attach jquery to window
window.$ = $

// register some basic usability functionality
$(document)
  .ready(() => {
    // give instant feedback on form submission
    $('form')
      .on('submit', () => {
        const $form = $(this)
        const $buttons = $('.btn', $form)
        if (!$buttons.hasClass('no-disable')) {
          $buttons.addClass('disabled')
        }
      })

    $('[data-toggle="popover"]')
      .popover()

    $('select').selectpicker()

    const data = [
      {
        value: 'Zurich Mainstation',
        label: 'Zurich Mainstation'
      },
      {
        value: 'Zurich Airport (ZRH)',
        label: 'Zurich Airport (ZRH)'
      }
    ]
    $('#edit_travel_group_location').autocompleter({ source: data })

    $('input[type=datetime-local]').flatpickr({
      enableTime: true,
      altFormat: 'F j, Y H:i',
      altInput: true,
      dateFormat: 'Z',
      time_24hr: true
    })

    $('.custom-file-input').on('change', function (event) {
      const fileName = event.target.files[0].name
      $(this).next('.custom-file-label').html(fileName)
    })

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault()

        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        })
      })
    })

    // force reload on user browser button navigation
    $(window)
      .on('popstate', () => {
        window.location.reload(true)
      })
  })
