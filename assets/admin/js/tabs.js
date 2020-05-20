function toggleActiveForIndex(elements, target) {
    for (let i = 0; i < elements.length; i++) {
        if (i === target) {
            elements[i].classList.add('active');
        } else {
            elements[i].classList.remove('active');
        }
    }
}

export default function () {
    const tabs = document.querySelectorAll('.nav-tabs');
    for (let i = 0; i < tabs.length; i++) {
        const tabsItems = tabs[i].querySelectorAll('.nav-item');
        const tabsContent = tabs[i].parentElement.querySelectorAll('.tab-pane');

        for (let j = 0; j < tabsItems.length; j++) {
            const tabItemLink = tabsItems[j];
            tabItemLink.addEventListener('click', () => {
                if (!tabItemLink.classList.contains('active')) {
                    toggleActiveForIndex(tabsContent, j);

                    for (let k = 0; k < tabsItems.length; k++) {
                        const link = tabsItems[k].querySelector('.nav-link');
                        if (k === j) {
                            link.classList.add('active');
                        } else {
                            link.classList.remove('active');
                        }
                    }
                }
            });
        }
    }
}
