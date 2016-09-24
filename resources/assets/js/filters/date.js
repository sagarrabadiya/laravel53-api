/**
 * Created by sagar on 30/08/16.
 */
import moment from "moment";

export default (date) => {
	let momentObject = new moment(date);
	return momentObject.format('DD-MM-YYYY HH:mm');
}
