<template>
  <div>
    <FullCalendar :options="calendarOptions" :data="calendarData"/>
  </div>
</template>

<script>
import '@fullcalendar/core/vdom'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import ptLocale from '@fullcalendar/core/locales/pt'
import Resource from '@/api/resource';
import AbsenceResource from '@/api/absences';

const absenceResource = new Resource('absences');

const eventResource = new AbsenceResource();

export default {  
    emits: ["openModal"],
    components:{
        FullCalendar,
    },
    props: {
        data:{
            type: Object,
            default: null,
        },
        events:{
            type: Object,
            default:{

            }
        } 
    },

    data() {
        return {
            loading: false,
            calendarData: [],
            calendarOptions:{ 
                plugins: [dayGridPlugin,timeGridPlugin,interactionPlugin],
                initialView: 'dayGridMonth',
                selectable: true,
                locale: ptLocale,
                headerToolbar: {
                    left: 'prev,next,today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek',
                },
                editable: true,
                events: [],
                eventColor: '',
                select: (arg) => {
                    var data = {};
                    // data.start_date = arg.startStr;
                    // data.end_date = arg.endStr;
                    data.date_range = [arg.startStr, arg.endStr];
                    this.$emit('OpenModal', data, true);
                },
                eventClick: (info) => {
                    var data = {};
                    data.id = info.event.id;
                    data.user_id = info.event.extendedProps.user_id;
                    data.name = info.event.title;
                    data.type = info.event.extendedProps.type_id;
                    // data.start_date = info.event.startStr;
                    // data.end_date = info.event.endStr;
                    data.date_range = [info.event.startStr, info.event.endStr];
                    this.$emit('OpenModal', data, false);
                }
            },
        }
    },

    watch:{
        events: {
            handler() {
                this.calendarOptions.events = this.events;
            },
            deep: true,
        },
    },

    methods:{
       
    }
}
</script>

<style lang="scss" scoped>
    .template {
        background: white !important;
    }
</style>