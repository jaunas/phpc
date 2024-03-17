use std::{
    fmt,
    ops::{Add, Div, Mul, Sub},
};

pub mod functions;

#[derive(PartialEq, PartialOrd, Debug, Clone)]
pub enum Value {
    Null,
    String(String),
    Number(f64),
    Bool(bool),
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
            Value::Bool(_) => "bool",
        }
        .to_string()
    }

    pub fn to_bool(self) -> bool {
        match self {
            Value::Null => todo!(),
            Value::String(_) => todo!(),
            Value::Number(_) => todo!(),
            Value::Bool(bool) => bool,
        }
    }
}

impl fmt::Display for Value {
    fn fmt(&self, f: &mut fmt::Formatter<'_>) -> fmt::Result {
        match self {
            Value::Null => write!(f, ""),
            Value::String(string) => write!(f, "{}", string),
            Value::Number(number) => write!(f, "{}", Value::trim(*number)),
            Value::Bool(bool) => write!(f, "{}", if *bool { "1" } else { "" }),
        }
    }
}

impl TryFrom<Value> for (f64, bool) {
    type Error = ();

    fn try_from(value: Value) -> Result<Self, Self::Error> {
        match value {
            Value::Null => Ok((0.0, false)),
            Value::String(string) => convert_string_to_float(string),
            Value::Number(number) => Ok((number, false)),
            Value::Bool(bool) => Ok((if bool { 1.0 } else { 0.0 }, false)),
        }
    }
}

fn convert_string_to_float(string: String) -> Result<(f64, bool), ()> {
    let regex = regex::Regex::new(r"^\s*(?P<number>-?\d+(\.\d+)?)\s*(?P<suffix>.*)$").unwrap();
    let Some(captures) = regex.captures(&string) else {
        return Err(());
    };

    Ok((
        captures["number"].parse::<f64>().unwrap(),
        !captures["suffix"].is_empty(),
    ))
}

fn unwrap_floats_or_panic(lhs: Value, rhs: Value) -> (f64, f64) {
    let lhs_type = lhs.type_name();
    let float_lhs: Result<(f64, bool), ()> = lhs.try_into();

    let rhs_type = rhs.type_name();
    let float_rhs: Result<(f64, bool), ()> = rhs.try_into();

    if float_lhs.is_err() || float_rhs.is_err() {
        panic!(
            "TypeError: Unsupported operand types: {} + {}",
            lhs_type, rhs_type
        );
    }

    (float_lhs.unwrap().0, float_rhs.unwrap().0)
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
        //         let prev_hook = panic::take_hook();
        //         panic::set_hook(Box::new(|_| {}));
        let result = panic::catch_unwind(f);
        //         panic::set_hook(prev_hook);
        result
    }

    #[test]
    fn compare_equal() {
        let pairs = vec![
            (Value::Null, Value::Null),
            (
                Value::String("equal".to_string()),
                Value::String("equal".to_string()),
            ),
            (Value::Number(5.0), Value::Number(5.0)),
            (Value::Bool(false), Value::Bool(false)),
        ];

        for (left, right) in pairs {
            assert_eq!(left, right);
        }
    }

    #[test]
    fn compare_not_equal() {
        let pairs = vec![
            (
                Value::String("left".to_string()),
                Value::String("right".to_string()),
            ),
            (Value::Number(5.0), Value::Number(3.14)),
            (Value::String("text".to_string()), Value::Null),
            (Value::Number(3.14), Value::String("3.14".to_string())),
            (Value::Bool(true), Value::Bool(false)),
        ];

        for (left, right) in pairs {
            assert_ne!(left, right);
        }
    }

    #[test]
    fn compare_less() {
        let examples = vec![
            (false, (Value::Null, Value::Null)),
            (true, (Value::Number(3.14), Value::Number(5.0))),
            (false, (Value::Number(5.0), Value::Number(3.14))),
        ];

        for (expected, (left, right)) in examples {
            assert_eq!(expected, left < right);
        }
    }

    #[test]
    fn to_string() {
        let examples = vec![
            ("", Value::Null),
            ("text", Value::String("text".to_string())),
            ("5", Value::Number(5.0)),
            ("1.6666666666667", Value::Number(5.0 / 3.0)),
            ("", Value::Bool(false)),
            ("1", Value::Bool(true)),
        ];

        for (expected, value) in examples {
            assert_eq!(expected, value.to_string());
        }
    }

    #[test]
    fn concat_strings() {
        let left_string = Value::String("left".to_string());
        let right_string = Value::String("right".to_string());

        let concat = left_string.concat(right_string);
        assert_eq!(Value::String("leftright".to_string()), concat);
    }

    // Cast to float
    #[test]
    fn to_float() {
        let examples = vec![
            ((0.0, false), Value::Null),
            ((5.0, false), Value::Number(5.0)),
            ((5.0, false), Value::String("5".to_string())),
            ((3.14, false), Value::String(" 3.14 ".to_string())),
            ((3.14, true), Value::String("3.14foobar".to_string())),
            ((0.0, false), Value::Bool(false)),
            ((1.0, false), Value::Bool(true)),
        ];

        for (expected, value) in examples {
            let float: Result<(f64, bool), ()> = value.try_into();
            assert!(float.is_ok());
            assert_eq!(expected, float.unwrap());
        }
    }

    #[test]
    fn to_float_failure() {
        let values = vec![
            Value::String("text".to_string()),
            Value::String("foobar3.14".to_string()),
        ];

        for value in values {
            let float: Result<(f64, bool), ()> = value.try_into();
            assert!(float.is_err());
        }
    }

    #[test]
    fn to_bool() {
        let values = vec![(true, Value::Bool(true)), (false, Value::Bool(false))];

        for (expected, value) in values {
            assert_eq!(expected, value.to_bool());
        }
    }

    #[test]
    fn type_of() {
        let examples = vec![
            ("null", Value::Null),
            ("number", Value::Number(5.0)),
            ("string", Value::String("text".to_string())),
            ("bool", Value::Bool(true)),
        ];

        for (expected, value) in examples {
            assert_eq!(expected, value.type_name());
        }
    }

    // Add
    #[test]
    fn math_operations() {
        let examples = vec![
            (
                (Value::Number(0.0), Value::Number(0.0), Value::Number(0.0)),
                (Value::Null, Value::Null),
            ),
            (
                (
                    Value::Number(3.14),
                    Value::Number(-3.14),
                    Value::Number(0.0),
                ),
                (Value::Null, Value::Number(3.14)),
            ),
            (
                (Value::Number(3.14), Value::Number(3.14), Value::Number(0.0)),
                (Value::Number(3.14), Value::Null),
            ),
            (
                (
                    Value::Number(1.8599999999999999),
                    Value::Number(-8.14),
                    Value::Number(-15.700000000000001),
                ),
                (
                    Value::String(" -3.14foobar".to_string()),
                    Value::Number(5.0),
                ),
            ),
        ];

        for ((add, sub, mul), (left, right)) in examples {
            macro_rules! assert_opperation {
                ($expected:ident, $op:tt) => {
                    assert_eq!($expected, left.clone() $op right.clone(), "{:?} {} {:?}", left.clone(), stringify!($op), right.clone());
                };
            }

            assert_opperation!(add, +);
            assert_opperation!(sub, -);
            assert_opperation!(mul, *);
        }
    }

    #[test]
    fn divide() {
        let examples = vec![
            (Value::Number(0.0), (Value::Null, Value::Number(3.14))),
            (
                Value::Number(-0.628),
                (
                    Value::String(" -3.14foobar".to_string()),
                    Value::Number(5.0),
                ),
            ),
        ];

        for (expected, (left, right)) in examples {
            assert_eq!(
                expected,
                left.clone() / right.clone(),
                "{:?} / {:?}",
                left.clone(),
                right.clone()
            );
        }
    }

    #[test]
    fn incorrect_math_operations() {
        let examples = vec![
            (
                "TypeError: Unsupported operand types: null + string",
                (Value::Null, Value::String("text".to_string())),
            ),
            (
                "TypeError: Unsupported operand types: string + null",
                (Value::String("text".to_string()), Value::Null),
            ),
            (
                "TypeError: Unsupported operand types: string + number",
                (Value::String("foobar-3.14".to_string()), Value::Number(5.0)),
            ),
        ];

        for (expected, (left, right)) in examples {
            let result = catch_unwind_silent(|| left + right);
            assert!(result.is_err());

            let err = result.unwrap_err();
            let msg = err.downcast::<String>().unwrap();
            assert_eq!(expected, msg.to_string());
        }
    }
}
