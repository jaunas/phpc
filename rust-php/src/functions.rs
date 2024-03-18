use crate::Value;

pub trait Function {
    fn call(args: Vec<Value>) -> FunctionResult;
}

pub struct FunctionResult {
    echo: String,
}

impl FunctionResult {
    pub fn unwrap(&self) {
        print!("{}", self.echo);
    }
}

pub struct Echo;
impl Function for Echo {
    fn call(args: Vec<Value>) -> FunctionResult {
        FunctionResult {
            echo: args.iter().map(|value| value.to_string()).collect(),
        }
    }
}

pub struct VarDump;
impl Function for VarDump {
    fn call(args: Vec<Value>) -> FunctionResult {
        let arg = args.first();
        let echo = match arg {
            Some(value) => match value {
                Value::Null => "NULL\n".to_string(),
                Value::String(string) => format!("string({}) \"{}\"\n", string.len(), string),
                Value::Number(number) => {
                    let number_type = if number.fract() == 0.0 {
                        "int"
                    } else {
                        "float"
                    };
                    format!("{}({})\n", number_type, value.to_string())
                }
                Value::Bool(bool) => format!("bool({})\n", bool.to_string()),
            },
            None => "".to_string(),
        };

        FunctionResult { echo }
    }
}

#[cfg(test)]
mod tests {
    use crate::Value;

    use super::*;

    #[test]
    fn echo_nothing() {
        assert_eq!("", Echo::call(vec![]).echo);
    }

    #[test]
    fn echo_string() {
        let args = vec![Value::String("text".to_string())];
        assert_eq!("text", Echo::call(args).echo);
    }

    #[test]
    fn echo_multiple_values() {
        let args = vec![
            Value::String("Value::Null -> ".to_string()),
            Value::Null,
            Value::String("\n".to_string()),
            Value::String("Value::String(\"text\".to_string()) -> ".to_string()),
            Value::String("text".to_string()),
            Value::String("\n".to_string()),
            Value::String("Value::Number(5.0) -> ".to_string()),
            Value::Number(5.0),
            Value::String("\n".to_string()),
            Value::String("Value::Bool(true) -> ".to_string()),
            Value::Bool(true),
            Value::String("\n".to_string()),
        ];

        let expected = r#"Value::Null -> 
Value::String("text".to_string()) -> text
Value::Number(5.0) -> 5
Value::Bool(true) -> 1
"#;
        assert_eq!(expected, Echo::call(args).echo);
    }

    #[test]
    fn var_dump_true() {
        let examples = vec![
            ("NULL\n", Value::Null),
            ("bool(true)\n", Value::Bool(true)),
            (
                "string(12) \"example text\"\n",
                Value::String("example text".to_string()),
            ),
            ("float(3.14)\n", Value::Number(3.14)),
            ("int(5)\n", Value::Number(5.0)),
        ];

        for (expected, value) in examples {
            assert_eq!(expected, VarDump::call(vec![value]).echo);
        }
    }
}
