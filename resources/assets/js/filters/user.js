/**
 * Created by sagar on 30/08/16.
 */

export default (user) => {
	if(_.isObject(user)) {
		return user.firstname.charAt(0).toUpperCase() + user.firstname.substr(1) + " " + user.lastname.charAt(0).toUpperCase() + ".";
	}

	return 'Unassigned';
}
