use std::fmt;

pub struct PhpNumber {
    value: f64,
}

impl PhpNumber {
    pub fn new(value: f64) -> Self {
        PhpNumber { value }
    }

    fn trim_leading_zeroes(&self) -> String {
        let with_precision = format!("{:.13}", self.value);
        return with_precision
            .trim_end_matches('0')
            .trim_end_matches('.')
            .to_owned();
    }
}

impl fmt::Display for PhpNumber {
    fn fmt(&self, f: &mut fmt::Formatter<'_>) -> fmt::Result {
        write!(f, "{}", self.trim_leading_zeroes())
    }
}

#[derive(PartialEq)]
pub enum Value {
    Null,
    String(String),
}

impl Value {
    pub fn concat(self, other: Self) -> Self {
        Value::String(self.to_string() + &other.to_string())
    }
}

impl fmt::Display for Value {
    fn fmt(&self, f: &mut fmt::Formatter<'_>) -> fmt::Result {
        match self {
            Value::Null => write!(f, ""),
            Value::String(string) => write!(f, "{}", string),
        }
    }
}

pub fn concat(left: Value, right: Value) -> Value {
    Value::String(left.to_string() + &right.to_string())
}

#[cfg(test)]
mod tests {
    use super::*;

    #[test]
    fn create_null_value() {
        let _null = Value::Null;
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
    fn create_string_value() {
        let _string = Value::String("example string".to_string());
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
}
