import {
  library,
  config,
  dom
} from '@fortawesome/fontawesome-svg-core'
import {
  faPlus,
  faPencil,
  faTrash,
  faUserAlt,
  faStar,
  faQuestionCircle,
  faEnvelopeOpen
} from '@fortawesome/pro-light-svg-icons'
import { faStar as faStartSolid } from '@fortawesome/pro-solid-svg-icons/faStar'
import '@fortawesome/fontawesome-svg-core/styles.css'

// configure fontawesome
config.autoAddCss = false
library.add(
  faPlus,
  faPencil,
  faTrash,
  faUserAlt,
  faStar,
  faQuestionCircle,
  faEnvelopeOpen,

  faStartSolid
)
dom.watch()
