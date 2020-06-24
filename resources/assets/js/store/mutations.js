const mutations = {
    removeUserNotification(state, id) {
        const index = state.userNotifications.findIndex(notification => {
            return notification.id === id
        });
        if (index !== -1) {
            state.userNotifications.splice(index, 1);
        }
    },
    addUserNotification(state, {text, type, id}) {

        state.userNotifications.push({
            text,
            type,
            id
        });
    },
    setUserInfo(state, {name}) {
        state.user.name = name;
    },
    toggleMobileMenu(state) {
        state.menuExpanded = !state.menuExpanded
    },
    collapseMobileMenu(state) {
        state.menuExpanded = false;
    },
    setProductsCount(state, count) {
        state.products.count = count;
    }
};

export default mutations;