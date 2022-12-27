import moment from 'moment';
export default {

    methods: {
        formDate(value) {
            if (value) {
                return moment(String(value)).format('DD/MM/YYYY, HH:mm:ss')
            }
        },
        localDate(date) {
            var d = new Date(date)
            return d.toLocaleString()
        }
    }
}