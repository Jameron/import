class HideMessage {
  constructor (target) {
    this.target = target
    this.targetObject = document.getElementById(target)
    this.setListenForTransitionEndEvent()
  }

  dump () {
    console.log(this.target)
  }

  setListenForTransitionEndEvent () {
    /* Figure out which browser, return the appropriate syntax for transition end. */
    function whichTransitionEvent () {
      var t
      var el = document.createElement('div')
      var transitions = {
        'transition': 'transitionend',
        'OTransition': 'oTransitionEnd',
        'MozTransition': 'transitionend',
        'WebkitTransition': 'webkitTransitionEnd'
      }

      for (t in transitions) {
        if (el.style[t] !== undefined) {
          return transitions[t]
        }
      }
    }

    /* Add the transition end event listener */
    var that = this
    var transitionEvent = whichTransitionEvent()
    transitionEvent && this.targetObject.addEventListener(transitionEvent, function () {
      that.hideMessage()
    })

    this.targetObject.className += ' fadeout'
  }

  hideMessage () {
    this.targetObject.style.display = 'none'
  }
}

export default HideMessage
