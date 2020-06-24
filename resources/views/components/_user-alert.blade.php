<div

        class="user-alerts__container">
    <transition-group tag="div" name="fade">
        <div
                :key="index"
                v-for="(notification, index) in userNotifications"
                :class="['user-alert', notification.type]"

                >
            <div v-text="notification.text"></div>
            <div
                    @click="()=>removeUserNotificationById(notification.id)"
                    :class="{'user-alert__close-icon__container': true}">
                <i :class="['fas', 'fa-times', 'user-alert__close-icon', notification.type+'-icon']"></i>
            </div>

        </div>
    </transition-group>
</div>
