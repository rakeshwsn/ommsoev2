//--------------------------------------------------------------------
// Tabs
//--------------------------------------------------------------------

const tabLinks = [];
const contentDivs = [];

function init() {
  const tabListItems = document.getElementById('tabs').children;

  Array.from(tabListItems).forEach((item) => {
    if (item.nodeName === "LI") {
      const tabLink = item.querySelector('a');
      const id = getHash(tabLink.dataset.href);
      tabLinks[id] = tabLink;
      contentDivs[id] = document.getElementById(id);
    }
  });

  tabLinks.forEach((link, i) => {
    link.addEventListener('click', showTab);
    link.addEventListener('focus', () => {
      link.blur();
    });
    if (i === 0) {
      link.classList.add('active');
    }
  });

  let i = 0;
  Array.from(contentDivs).forEach((div) => {
    if (i !== 0) {
      div.classList.add('content', 'hide');
    }
    i++;
  });
}

//--------------------------------------------------------------------

function showTab() {
  const selectedId = getHash(this.dataset.href);

  for (const id in contentDivs) {
    if (id === selectedId) {
      tabLinks[id].classList.add('active');
      contentDivs[id].classList.remove('hide');
    } else {
      tabLinks[id].classList.remove('active');
      contentDivs[id].classList.add('hide');
    }
  }

  return false;
}

//--------------------------------------------------------------------

function getFirstChildWithTagName(element, tagName) {
  for (let i = 0; i < element.childNodes.length; i++) {
    if (element.childNodes[i].nodeName === tagName) {
      return element.childNodes[i];
    }
  }
}

//--------------------------------
