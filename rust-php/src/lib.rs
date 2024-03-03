use std::{
    fmt,
    ops::{Add, Div, Mul, Sub},
};

pub struct PhpNumber {
    value: f64,
}

impl PhpNumber {
    pub fn new(value: f64) -> Self {
        PhpNumber { value }
    }

    fn trim_leading_zeroes(&self) -> String {
        format!("{:.13}", self.value)
            .trim_end_matches('0')
            .trim_end_matches('.')
            .to_owned()
    }
}

impl fmt::Display for PhpNumber {
    fn fmt(&self, f: &mut fmt::Formatter<'_>) -> fmt::Result {
        write!(f, "{}", self.trim_leading_zeroes())
    }
}

#[derive(PartialEq, Debug)]
pub enum Value {
    Null,
    String(String),
    Number(f64),
}

impl Value {
    pub fn concat(self, other: Self) -> Self {
        Value::String(self.to_string() + &other.to_string())
    }

    fn trim(number: f64) -> String {
        format!("{:.13}", number)
            .trim_end_matches('0')
            .trim_end_matches('.')
            .to_string()
    }

    pub fn type_name(&self) -> String {
        match self {
            Value::Null => "null",
            Value::String(_) => "string",
            Value::Number(_) => "number",
        }
        .to_string()
    }
}

impl fmt::Display for Value {
    fn fmt(&self, f: &mut fmt::Formatter<'_>) -> fmt::Result {
        match self {
            Value::Null => write!(f, ""),
            Value::String(string) => write!(f, "{}", string),
            Value::Number(number) => write!(f, "{}", Value::trim(*number)),
        }
    }
}

impl TryFrom<Value> for f64 {
    type Error = ();

    fn try_from(value: Value) -> Result<Self, Self::Error> {
        match value {
            Value::Null => Ok(0.0),
            Value::String(_) => Err(()),
            Value::Number(number) => Ok(number),
        }
    }
}

fn unwrap_floats_or_panic(lhs: Value, rhs: Value) -> (f64, f64) {
    let lhs_type = lhs.type_name();
    let float_lhs: Result<f64, ()> = lhs.try_into();

    let rhs_type = rhs.type_name();
    let float_rhs: Result<f64, ()> = rhs.try_into();

    if float_lhs.is_err() || float_rhs.is_err() {
        panic!(
            "TypeError: Unsupported operand types: {} + {}",
            lhs_type, rhs_type
        );
    }

    (float_lhs.unwrap(), float_rhs.unwrap())
}

macro_rules! impl_binary_op {
    ($trait:ident, $method:ident, $op:tt) => {
        impl $trait for Value {
            type Output = Value;

            fn $method(self, rhs: Self) -> Self::Output {
                let (float_lhs, float_rhs) = unwrap_floats_or_panic(self, rhs);
                Value::Number(float_lhs $op float_rhs)
            }
        }
    };
}

impl_binary_op!(Add, add, +);
impl_binary_op!(Sub, sub, -);
impl_binary_op!(Mul, mul, *);
impl_binary_op!(Div, div, /);

#[cfg(test)]
mod tests {
    use super::*;
    use std::panic;

    fn catch_unwind_silent<F: FnOnce() -> R + panic::UnwindSafe, R>(
        f: F,
    ) -> std::thread::Result<R> {
        let prev_hook = panic::take_hook();
        panic::set_hook(Box::new(|_| {}));
        let result = panic::catch_unwind(f);
        panic::set_hook(prev_hook);
        result
    }

    #[test]
    fn compare_nulls() {
        let left = Value::Null;
        let right = Value::Null;
        assert!(left == right);
    }

    #[test]
    fn null_to_string() {
        let null = Value::Null;
        assert_eq!("", null.to_string());
    }

    #[test]
    fn compare_different_strings() {
        let left = Value::String("left".to_string());
        let right = Value::String("right".to_string());
        assert!(!(left == right));
    }

    #[test]
    fn compare_equal_strings() {
        let left = Value::String("equal".to_string());
        let right = Value::String("equal".to_string());
        assert!(left == right);
    }

    #[test]
    fn compare_string_and_null() {
        let string = Value::String("string".to_string());
        let null = Value::Null;
        assert!(!(string == null));
    }

    #[test]
    fn string_to_string() {
        let string = Value::String("string".to_string());
        assert_eq!("string", string.to_string());
    }

    #[test]
    fn concat_strings() {
        let left_string = Value::String("left".to_string());
        let right_string = Value::String("right".to_string());

        let concat = left_string.concat(right_string);
        assert!(Value::String("leftright".to_string()) == concat);
    }

    #[test]
    fn number_int_to_string() {
        let number = Value::Number(5_f64);
        assert_eq!("5", number.to_string());
    }

    #[test]
    fn number_float_to_string() {
        let number = Value::Number(5_f64 / 3_f64);
        assert_eq!("1.6666666666667", number.to_string());
    }

    // Cast to float

    #[test]
    fn null_to_float() {
        let number = Value::Null;
        let float: Result<f64, ()> = number.try_into();
        assert!(float.is_ok());
        assert_eq!(0.0, float.unwrap());
    }

    #[test]
    fn string_to_float() {
        let string = Value::String("text".to_string());
        let float: Result<f64, ()> = string.try_into();
        assert!(float.is_err());
    }

    #[test]
    fn number_to_float() {
        let number = Value::Number(3.14);
        let float: Result<f64, ()> = number.try_into();
        assert!(float.is_ok());
        assert_eq!(3.14, float.unwrap());
    }

    #[test]
    fn type_of_null_value() {
        assert_eq!("null", Value::Null.type_name());
    }

    #[test]
    fn type_of_string_value() {
        assert_eq!("string", Value::String("text".to_string()).type_name());
    }

    // Add

    #[test]
    fn null_plus_null() {
        let result = Value::Null + Value::Null;
        assert_eq!(Value::Number(0.0), result);
    }

    #[test]
    fn null_plus_number() {
        let result = Value::Null + Value::Number(3.14);
        assert_eq!(Value::Number(3.14), result);
    }

    #[test]
    fn number_plus_null() {
        let result = Value::Number(3.14) + Value::Null;
        assert_eq!(Value::Number(3.14), result);
    }

    #[test]
    fn null_plus_string() {
        let result = catch_unwind_silent(|| Value::Null + Value::String("text".to_string()));
        assert!(result.is_err());
        let err = result.unwrap_err();
        let msg = err.downcast::<String>().unwrap();
        assert_eq!(
            "TypeError: Unsupported operand types: null + string",
            msg.to_string()
        );
    }

    #[test]
    fn string_plus_null() {
        let result = catch_unwind_silent(|| Value::String("text".to_string()) + Value::Null);
        assert!(result.is_err());
        let err = result.unwrap_err();
        let msg = err.downcast::<String>().unwrap();
        assert_eq!(
            "TypeError: Unsupported operand types: string + null",
            msg.to_string()
        );
    }

    #[test]
    fn number_minus_number() {
        let result = Value::Number(3.14) - Value::Number(5.0);
        assert_eq!(Value::Number(-1.8599999999999999), result);
    }

    #[test]
    fn number_multiply_number() {
        let result = Value::Number(3.14) * Value::Number(5.0);
        assert_eq!(Value::Number(15.700000000000001), result);
    }

    #[test]
    fn number_divide_number() {
        let result = Value::Number(3.14) / Value::Number(5.0);
        assert_eq!(Value::Number(0.628), result);
    }
}
